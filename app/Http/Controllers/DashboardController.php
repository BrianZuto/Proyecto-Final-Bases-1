<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard con datos reales de la base de datos
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Cargar la relación rol si no está cargada
        if ($user && !$user->relationLoaded('rol')) {
            $user->load('rol');
        }
        
        $planActivo = $user->planActivo();

        // Obtener o crear deportista
        $deportista = DB::select("SELECT * FROM deportistas WHERE user_id = ? LIMIT 1", [$user->id]);
        
        if (empty($deportista)) {
            DB::insert("INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)", [
                $user->id,
                now(),
                now()
            ]);
            $deportistaId = DB::getPdo()->lastInsertId();
            $deportista = (object) ['id' => $deportistaId];
        } else {
            $deportista = $deportista[0];
        }

        // Estadísticas de la semana actual
        $inicioSemana = now()->startOfWeek()->toDateString();
        $finSemana = now()->endOfWeek()->toDateString();

        // Rutinas completadas esta semana
        $rutinasCompletadasSemana = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM rutina_usuario_progreso 
            WHERE user_id = ? 
            AND estado = 'completada' 
            AND fecha_ultima_sesion BETWEEN ? AND ?
        ", [$user->id, $inicioSemana, $finSemana])->total ?? 0;

        // Total de rutinas asignadas
        $totalRutinas = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM rutina_usuario_progreso 
            WHERE user_id = ?
        ", [$user->id])->total ?? 0;

        // Calorías quemadas esta semana
        $caloriasSemana = DB::selectOne("
            SELECT COALESCE(SUM(calorias_quemadas), 0) as total 
            FROM sesiones 
            WHERE user_id = ? 
            AND estado = 'completada' 
            AND fecha_sesion BETWEEN ? AND ?
        ", [$user->id, $inicioSemana, $finSemana])->total ?? 0;

        // Calorías semana pasada para comparación
        $inicioSemanaPasada = now()->subWeek()->startOfWeek()->toDateString();
        $finSemanaPasada = now()->subWeek()->endOfWeek()->toDateString();
        $caloriasSemanaPasada = DB::selectOne("
            SELECT COALESCE(SUM(calorias_quemadas), 0) as total 
            FROM sesiones 
            WHERE user_id = ? 
            AND estado = 'completada' 
            AND fecha_sesion BETWEEN ? AND ?
        ", [$user->id, $inicioSemanaPasada, $finSemanaPasada])->total ?? 0;

        $porcentajeCalorias = $caloriasSemanaPasada > 0 
            ? (($caloriasSemana - $caloriasSemanaPasada) / $caloriasSemanaPasada) * 100 
            : 0;

        // Tiempo total esta semana (en horas)
        $tiempoSemana = DB::selectOne("
            SELECT COALESCE(SUM(duracion_minutos), 0) as total 
            FROM sesiones 
            WHERE user_id = ? 
            AND estado = 'completada' 
            AND fecha_sesion BETWEEN ? AND ?
        ", [$user->id, $inicioSemana, $finSemana])->total ?? 0;
        $tiempoSemanaHoras = round($tiempoSemana / 60, 1);

        // Peso levantado (estimado basado en ejercicios completados)
        $pesoLevantado = DB::selectOne("
            SELECT COALESCE(SUM(COALESCE(dr.peso, 0) * COALESCE(dr.series, 1) * COALESCE(dr.repeticiones, 1)), 0) as total
            FROM progresos p
            INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
            INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
            WHERE p.deportista_id = ?
            AND p.fecha_registro BETWEEN ? AND ?
            AND p.porcentaje_completado >= 100
        ", [$deportista->id, $inicioSemana, $finSemana])->total ?? 0;
        $pesoLevantadoToneladas = round($pesoLevantado / 1000, 1);

        // Progreso reciente (últimos ejercicios completados)
        $progreso_reciente = DB::select("
            SELECT 
                e.nombre as ejercicio,
                p.fecha_registro,
                dr.peso,
                dr.repeticiones
            FROM progresos p
            INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
            INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
            WHERE p.deportista_id = ?
            AND p.porcentaje_completado >= 100
            ORDER BY p.fecha_registro DESC
            LIMIT 5
        ", [$deportista->id]);

        $progreso_reciente = collect($progreso_reciente)->map(function($item) {
            return [
                'ejercicio' => $item->ejercicio,
                'anterior' => 'N/A',
                'actual' => $item->peso ? $item->peso . 'kg x ' . $item->repeticiones : 'Completado',
                'mejora' => '✓',
            ];
        });

        // Próximos entrenamientos (rutinas en progreso o pendientes)
        $proximos_entrenamientos = DB::select("
            SELECT 
                r.nombre,
                r.id as rutina_id,
                rup.fecha_ultima_sesion,
                rup.estado
            FROM rutina_usuario_progreso rup
            INNER JOIN rutinas r ON rup.rutina_id = r.id
            WHERE rup.user_id = ?
            AND rup.estado IN ('en_progreso', 'pendiente')
            ORDER BY 
                CASE WHEN rup.estado = 'en_progreso' THEN 0 ELSE 1 END,
                rup.fecha_ultima_sesion DESC
            LIMIT 5
        ", [$user->id]);

        $proximos_entrenamientos = collect($proximos_entrenamientos)->map(function($item) {
            if ($item->estado == 'en_progreso') {
                $fecha = $item->fecha_ultima_sesion 
                    ? \Carbon\Carbon::parse($item->fecha_ultima_sesion)->format('d/m/Y')
                    : 'Hoy';
            } else {
                $fecha = 'Pendiente';
            }
            return [
                'fecha' => $fecha,
                'rutina' => $item->nombre,
                'rutina_id' => $item->rutina_id,
            ];
        });

        $stats = [
            'completados' => $rutinasCompletadasSemana,
            'total' => $totalRutinas > 0 ? $totalRutinas : 1, // Evitar división por cero
            'calorias' => $caloriasSemana,
            'peso_levantado' => $pesoLevantadoToneladas,
            'tiempo_total' => $tiempoSemanaHoras,
            'porcentaje_calorias' => round($porcentajeCalorias, 1),
        ];
        
        return view('dashboard', compact('stats', 'progreso_reciente', 'proximos_entrenamientos', 'planActivo'));
    }
}
