<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProgresoController extends Controller
{
    /**
     * Muestra la vista de progreso del usuario
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

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

        // Estadísticas generales
        $stats = [
            'sesiones_totales' => DB::selectOne("SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$user->id])->total ?? 0,
            'sesiones_mes' => DB::selectOne("SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada' AND YEAR(fecha_sesion) = ? AND MONTH(fecha_sesion) = ?", [$user->id, now()->year, now()->month])->total ?? 0,
            'calorias_totales' => DB::selectOne("SELECT COALESCE(SUM(calorias_quemadas), 0) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$user->id])->total ?? 0,
            'tiempo_total' => DB::selectOne("SELECT COALESCE(SUM(duracion_minutos), 0) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$user->id])->total ?? 0,
            'rutinas_completadas' => DB::selectOne("SELECT COUNT(*) as total FROM rutina_usuario_progreso WHERE user_id = ? AND estado = 'completada'", [$user->id])->total ?? 0,
            'ejercicios_completados' => DB::selectOne("SELECT COUNT(DISTINCT detalle_rutina_id) as total FROM progresos WHERE deportista_id = ? AND porcentaje_completado >= 100", [$deportista->id])->total ?? 0,
        ];

        // Calcular racha actual (días consecutivos con sesiones)
        $rachaActual = self::calcularRacha($user->id);

        // Progreso de rutinas
        $rutinasProgreso = collect(DB::select("
            SELECT 
                r.id,
                r.nombre,
                rup.porcentaje_completado,
                rup.estado,
                rup.sesiones_completadas,
                rup.fecha_inicio,
                rup.fecha_ultima_sesion
            FROM rutina_usuario_progreso rup
            INNER JOIN rutinas r ON rup.rutina_id = r.id
            WHERE rup.user_id = ?
            ORDER BY rup.fecha_ultima_sesion DESC
            LIMIT 10
        ", [$user->id]));

        // Progreso de ejercicios (últimos 30 días)
        $progresoEjercicios = collect(DB::select("
            SELECT 
                e.nombre as ejercicio_nombre,
                p.fecha_registro,
                COUNT(*) as veces_completado
            FROM progresos p
            INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
            INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
            WHERE p.deportista_id = ?
            AND p.fecha_registro >= ?
            AND p.porcentaje_completado >= 100
            GROUP BY e.id, e.nombre, p.fecha_registro
            ORDER BY p.fecha_registro DESC
        ", [$deportista->id, now()->subDays(30)->toDateString()]));

        // Gráfico de sesiones por mes (últimos 6 meses)
        $sesionesPorMes = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mes = $fecha->format('Y-m');
            $mesNombre = $fecha->format('M Y');
            
            $cantidad = DB::selectOne("
                SELECT COUNT(*) as total 
                FROM sesiones 
                WHERE user_id = ? 
                AND estado = 'completada' 
                AND YEAR(fecha_sesion) = ? 
                AND MONTH(fecha_sesion) = ?
            ", [$user->id, $fecha->year, $fecha->month])->total ?? 0;
            
            $sesionesPorMes[] = [
                'mes' => $mesNombre,
                'cantidad' => $cantidad,
            ];
        }

        // Evolución de peso (si existe en deportistas)
        $evolucionPeso = DB::select("
            SELECT peso, updated_at 
            FROM deportistas 
            WHERE user_id = ? 
            AND peso IS NOT NULL 
            ORDER BY updated_at DESC 
            LIMIT 10
        ", [$user->id]);

        return view('progreso.index', compact(
            'stats',
            'rachaActual',
            'rutinasProgreso',
            'progresoEjercicios',
            'sesionesPorMes',
            'evolucionPeso'
        ));
    }

    /**
     * Muestra la vista de logros del usuario
     */
    public function logros()
    {
        /** @var User $user */
        $user = Auth::user();

        // Obtener deportista
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

        // Verificar y asignar logros automáticamente
        self::verificarYAsignarLogros($user->id, $deportista->id);

        // Obtener logros obtenidos
        $logrosObtenidos = DB::select("
            SELECT * 
            FROM gamificaciones 
            WHERE deportista_id = ? 
            ORDER BY fecha_asignacion DESC
        ", [$deportista->id]);
        $logrosObtenidos = collect($logrosObtenidos);

        // Calcular estadísticas de logros
        $statsLogros = [
            'total_logros' => $logrosObtenidos->count(),
            'puntos_totales' => $logrosObtenidos->sum('puntos'),
            'logros_mes' => $logrosObtenidos->where('fecha_asignacion', '>=', now()->startOfMonth()->toDateString())->count(),
        ];

        // Agrupar logros por categoría
        $logrosPorCategoria = $logrosObtenidos->groupBy(function ($logro) {
            // Categorizar logros por nombre
            if (stripos($logro->logro, 'sesión') !== false || stripos($logro->logro, 'sesiones') !== false) {
                return 'Sesiones';
            } elseif (stripos($logro->logro, 'rutina') !== false || stripos($logro->logro, 'rutinas') !== false) {
                return 'Rutinas';
            } elseif (stripos($logro->logro, 'ejercicio') !== false || stripos($logro->logro, 'ejercicios') !== false) {
                return 'Ejercicios';
            } elseif (stripos($logro->logro, 'racha') !== false || stripos($logro->logro, 'días') !== false) {
                return 'Rachas';
            } elseif (stripos($logro->logro, 'caloría') !== false || stripos($logro->logro, 'calorías') !== false) {
                return 'Calorías';
            } else {
                return 'Otros';
            }
        });

        // Logros disponibles (todos los posibles)
        $logrosDisponibles = $this->obtenerLogrosDisponibles();
        
        // Marcar cuáles ya se han obtenido
        $logrosObtenidosNombres = $logrosObtenidos->pluck('logro')->toArray();
        foreach ($logrosDisponibles as &$logro) {
            $logro['obtenido'] = in_array($logro['nombre'], $logrosObtenidosNombres);
            if ($logro['obtenido']) {
                $logroObtenido = $logrosObtenidos->firstWhere('logro', $logro['nombre']);
                $logro['fecha_obtencion'] = $logroObtenido->fecha_asignacion ?? null;
            }
        }

        return view('progreso.logros', compact(
            'logrosObtenidos',
            'statsLogros',
            'logrosPorCategoria',
            'logrosDisponibles'
        ));
    }

    /**
     * Calcula la racha actual de días consecutivos con sesiones
     */
    private static function calcularRacha($userId)
    {
        $sesiones = DB::select("
            SELECT DISTINCT fecha_sesion 
            FROM sesiones 
            WHERE user_id = ? 
            AND estado = 'completada' 
            ORDER BY fecha_sesion DESC
        ", [$userId]);
        
        $sesiones = collect($sesiones)->pluck('fecha_sesion')->toArray();

        if (empty($sesiones)) {
            return 0;
        }

        $racha = 0;
        $fechaActual = now()->toDateString();
        $fechaEsperada = $fechaActual;

        foreach ($sesiones as $fecha) {
            if ($fecha == $fechaEsperada || $fecha == date('Y-m-d', strtotime($fechaEsperada . ' -1 day'))) {
                $racha++;
                $fechaEsperada = date('Y-m-d', strtotime($fecha . ' -1 day'));
            } else {
                break;
            }
        }

        return $racha;
    }

    /**
     * Verifica y asigna logros automáticamente basado en el progreso
     */
    public static function verificarYAsignarLogros($userId, $deportistaId)
    {
        $hoy = now()->toDateString();

        // Logro: Primera sesión
        $primeraSesion = DB::selectOne("
            SELECT * 
            FROM sesiones 
            WHERE user_id = ? 
            AND estado = 'completada' 
            ORDER BY fecha_sesion ASC 
            LIMIT 1
        ", [$userId]);

        if ($primeraSesion) {
            self::asignarLogro($deportistaId, 'Primera Sesión Completada', 10, $hoy);
        }

        // Logro: 5 sesiones
        $sesiones5 = DB::selectOne("SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$userId])->total ?? 0;

        if ($sesiones5 >= 5) {
            self::asignarLogro($deportistaId, '5 Sesiones Completadas', 25, $hoy);
        }

        // Logro: 10 sesiones
        if ($sesiones5 >= 10) {
            self::asignarLogro($deportistaId, '10 Sesiones Completadas', 50, $hoy);
        }

        // Logro: 25 sesiones
        if ($sesiones5 >= 25) {
            self::asignarLogro($deportistaId, '25 Sesiones Completadas', 100, $hoy);
        }

        // Logro: 50 sesiones
        if ($sesiones5 >= 50) {
            self::asignarLogro($deportistaId, '50 Sesiones Completadas', 200, $hoy);
        }

        // Logro: 100 sesiones
        if ($sesiones5 >= 100) {
            self::asignarLogro($deportistaId, '100 Sesiones Completadas', 500, $hoy);
        }

        // Logro: Primera rutina completada
        $rutinasCompletadas = DB::selectOne("SELECT COUNT(*) as total FROM rutina_usuario_progreso WHERE user_id = ? AND estado = 'completada'", [$userId])->total ?? 0;

        if ($rutinasCompletadas >= 1) {
            self::asignarLogro($deportistaId, 'Primera Rutina Completada', 30, $hoy);
        }

        // Logro: 5 rutinas completadas
        if ($rutinasCompletadas >= 5) {
            self::asignarLogro($deportistaId, '5 Rutinas Completadas', 75, $hoy);
        }

        // Logro: Racha de 3 días
        $racha = self::calcularRacha($userId);
        if ($racha >= 3) {
            self::asignarLogro($deportistaId, 'Racha de 3 Días', 20, $hoy);
        }

        // Logro: Racha de 7 días
        if ($racha >= 7) {
            self::asignarLogro($deportistaId, 'Racha de 7 Días', 50, $hoy);
        }

        // Logro: Racha de 30 días
        if ($racha >= 30) {
            self::asignarLogro($deportistaId, 'Racha de 30 Días', 200, $hoy);
        }

        // Logro: 1000 calorías quemadas
        $caloriasTotales = DB::selectOne("SELECT COALESCE(SUM(calorias_quemadas), 0) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$userId])->total ?? 0;

        if ($caloriasTotales >= 1000) {
            self::asignarLogro($deportistaId, '1000 Calorías Quemadas', 40, $hoy);
        }

        // Logro: 5000 calorías quemadas
        if ($caloriasTotales >= 5000) {
            self::asignarLogro($deportistaId, '5000 Calorías Quemadas', 100, $hoy);
        }

        // Logro: 10000 calorías quemadas
        if ($caloriasTotales >= 10000) {
            self::asignarLogro($deportistaId, '10000 Calorías Quemadas', 250, $hoy);
        }
    }

    /**
     * Asigna un logro si no existe
     */
    private static function asignarLogro($deportistaId, $logroNombre, $puntos, $fecha)
    {
        $existe = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM gamificaciones 
            WHERE deportista_id = ? 
            AND logro = ?
        ", [$deportistaId, $logroNombre]);

        if ($existe->total == 0) {
            DB::insert("
                INSERT INTO gamificaciones (deportista_id, fecha_asignacion, logro, puntos, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?)
            ", [
                $deportistaId,
                $fecha,
                $logroNombre,
                $puntos,
                now(),
                now()
            ]);
        }
    }

    /**
     * Obtiene todos los logros disponibles
     */
    private function obtenerLogrosDisponibles()
    {
        return [
            [
                'nombre' => 'Primera Sesión Completada',
                'descripcion' => 'Completa tu primera sesión de entrenamiento',
                'puntos' => 10,
                'categoria' => 'Sesiones',
                'icono' => '🎯',
            ],
            [
                'nombre' => '5 Sesiones Completadas',
                'descripcion' => 'Completa 5 sesiones de entrenamiento',
                'puntos' => 25,
                'categoria' => 'Sesiones',
                'icono' => '🔥',
            ],
            [
                'nombre' => '10 Sesiones Completadas',
                'descripcion' => 'Completa 10 sesiones de entrenamiento',
                'puntos' => 50,
                'categoria' => 'Sesiones',
                'icono' => '💪',
            ],
            [
                'nombre' => '25 Sesiones Completadas',
                'descripcion' => 'Completa 25 sesiones de entrenamiento',
                'puntos' => 100,
                'categoria' => 'Sesiones',
                'icono' => '🏆',
            ],
            [
                'nombre' => '50 Sesiones Completadas',
                'descripcion' => 'Completa 50 sesiones de entrenamiento',
                'puntos' => 200,
                'categoria' => 'Sesiones',
                'icono' => '👑',
            ],
            [
                'nombre' => '100 Sesiones Completadas',
                'descripcion' => 'Completa 100 sesiones de entrenamiento',
                'puntos' => 500,
                'categoria' => 'Sesiones',
                'icono' => '🌟',
            ],
            [
                'nombre' => 'Primera Rutina Completada',
                'descripcion' => 'Completa tu primera rutina completa',
                'puntos' => 30,
                'categoria' => 'Rutinas',
                'icono' => '✅',
            ],
            [
                'nombre' => '5 Rutinas Completadas',
                'descripcion' => 'Completa 5 rutinas completas',
                'puntos' => 75,
                'categoria' => 'Rutinas',
                'icono' => '🎖️',
            ],
            [
                'nombre' => 'Racha de 3 Días',
                'descripcion' => 'Mantén una racha de 3 días consecutivos',
                'puntos' => 20,
                'categoria' => 'Rachas',
                'icono' => '🔥',
            ],
            [
                'nombre' => 'Racha de 7 Días',
                'descripcion' => 'Mantén una racha de 7 días consecutivos',
                'puntos' => 50,
                'categoria' => 'Rachas',
                'icono' => '⚡',
            ],
            [
                'nombre' => 'Racha de 30 Días',
                'descripcion' => 'Mantén una racha de 30 días consecutivos',
                'puntos' => 200,
                'categoria' => 'Rachas',
                'icono' => '💎',
            ],
            [
                'nombre' => '1000 Calorías Quemadas',
                'descripcion' => 'Quema un total de 1000 calorías',
                'puntos' => 40,
                'categoria' => 'Calorías',
                'icono' => '🔥',
            ],
            [
                'nombre' => '5000 Calorías Quemadas',
                'descripcion' => 'Quema un total de 5000 calorías',
                'puntos' => 100,
                'categoria' => 'Calorías',
                'icono' => '💪',
            ],
            [
                'nombre' => '10000 Calorías Quemadas',
                'descripcion' => 'Quema un total de 10000 calorías',
                'puntos' => 250,
                'categoria' => 'Calorías',
                'icono' => '🏆',
            ],
        ];
    }
}
