<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SesionController extends Controller
{
    /**
     * Muestra la lista de sesiones del usuario
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Construir query base con SQL directo
        $whereConditions = ["s.user_id = ?"];
        $params = [$user->id];

        // Filtros
        if ($request->filled('fecha_desde')) {
            $whereConditions[] = "s.fecha_sesion >= ?";
            $params[] = $request->fecha_desde;
        }
        if ($request->filled('fecha_hasta')) {
            $whereConditions[] = "s.fecha_sesion <= ?";
            $params[] = $request->fecha_hasta;
        }
        if ($request->filled('estado')) {
            $whereConditions[] = "s.estado = ?";
            $params[] = $request->estado;
        }
        if ($request->filled('rutina_id')) {
            $whereConditions[] = "s.rutina_id = ?";
            $params[] = $request->rutina_id;
        }

        $whereClause = implode(' AND ', $whereConditions);

        // Paginación
        $perPage = $request->get('per_page', 12);
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        // Contar total
        $total = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM sesiones s
            LEFT JOIN rutinas r ON s.rutina_id = r.id
            WHERE {$whereClause}
        ", $params)->total ?? 0;

        // Obtener sesiones
        $sesionesData = DB::select("
            SELECT 
                s.*,
                r.nombre as rutina_nombre,
                r.descripcion as rutina_descripcion
            FROM sesiones s
            LEFT JOIN rutinas r ON s.rutina_id = r.id
            WHERE {$whereClause}
            ORDER BY s.fecha_sesion DESC, s.hora_inicio DESC
            LIMIT ? OFFSET ?
        ", array_merge($params, [$perPage, $offset]));

        // Agregar estadísticas a cada sesión
        $sesiones = collect($sesionesData)->map(function ($sesion) {
            $ejercicios = DB::selectOne("
                SELECT COUNT(*) as total 
                FROM sesion_ejercicios 
                WHERE sesion_id = ?
            ", [$sesion->id])->total ?? 0;
            $sesion->total_ejercicios = $ejercicios;
            return $sesion;
        });

        // Crear paginador manual
        $sesiones = new \Illuminate\Pagination\LengthAwarePaginator(
            $sesiones,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Obtener rutinas del usuario para el filtro
        $rutinasUsuario = collect(DB::select("
            SELECT DISTINCT r.id, r.nombre
            FROM rutina_usuario_progreso rup
            INNER JOIN rutinas r ON rup.rutina_id = r.id
            WHERE rup.user_id = ?
        ", [$user->id]));

        // Estadísticas generales
        $stats = [
            'total_sesiones' => DB::selectOne("SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$user->id])->total ?? 0,
            'sesiones_mes' => DB::selectOne("SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada' AND YEAR(fecha_sesion) = ? AND MONTH(fecha_sesion) = ?", [$user->id, now()->year, now()->month])->total ?? 0,
            'calorias_totales' => DB::selectOne("SELECT COALESCE(SUM(calorias_quemadas), 0) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$user->id])->total ?? 0,
            'tiempo_total' => DB::selectOne("SELECT COALESCE(SUM(duracion_minutos), 0) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$user->id])->total ?? 0,
        ];

        return view('sesiones.index', compact('sesiones', 'rutinasUsuario', 'stats'));
    }

    /**
     * Muestra el detalle de una sesión
     */
    public function show($sesion)
    {
        /** @var User $user */
        $user = Auth::user();

        // Obtener sesión con rutina
        $sesionData = DB::selectOne("
            SELECT 
                s.*,
                r.nombre as rutina_nombre,
                r.descripcion as rutina_descripcion,
                r.tiempo_estimado_minutos,
                r.calorias_estimadas
            FROM sesiones s
            LEFT JOIN rutinas r ON s.rutina_id = r.id
            WHERE s.id = ? AND s.user_id = ?
        ", [$sesion, $user->id]);

        if (!$sesionData) {
            abort(404, 'Sesión no encontrada');
        }

        // Obtener ejercicios de la sesión
        $ejercicios = collect(DB::select("
            SELECT 
                se.*,
                e.nombre as ejercicio_nombre,
                e.descripcion as ejercicio_descripcion,
                e.imagen_url,
                c.nombre as categoria_nombre
            FROM sesion_ejercicios se
            INNER JOIN ejercicios e ON se.ejercicio_id = e.id
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE se.sesion_id = ?
        ", [$sesion]));

        return view('sesiones.show', compact('sesionData', 'ejercicios'));
    }

    /**
     * Muestra el formulario para crear una nueva sesión
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        // Obtener rutinas del usuario
        $rutinas = DB::select("
            SELECT DISTINCT r.id, r.nombre, r.descripcion
            FROM rutina_usuario_progreso rup
            INNER JOIN rutinas r ON rup.rutina_id = r.id
            WHERE rup.user_id = ? AND r.activo = 1
        ", [$user->id]);

        return view('sesiones.create', compact('rutinas'));
    }

    /**
     * Almacena una nueva sesión
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rutina_id' => 'nullable|exists:rutinas,id',
            'fecha_sesion' => 'required|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'duracion_minutos' => 'nullable|integer|min:1',
            'calorias_quemadas' => 'nullable|integer|min:0',
            'ejercicios_completados' => 'nullable|integer|min:0',
            'estado' => 'required|in:en_progreso,completada,cancelada',
            'notas' => 'nullable|string|max:1000',
            'rendimiento' => 'nullable|string|max:500',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Calcular duración si se proporcionan horas
        if ($request->filled('hora_inicio') && $request->filled('hora_fin')) {
            $inicio = \Carbon\Carbon::parse($request->fecha_sesion . ' ' . $request->hora_inicio);
            $fin = \Carbon\Carbon::parse($request->fecha_sesion . ' ' . $request->hora_fin);
            $duracion = $inicio->diffInMinutes($fin);
            $validated['duracion_minutos'] = $duracion;
        }

        $ejerciciosCompletados = $validated['ejercicios_completados'] ?? 0;

        DB::insert("
            INSERT INTO sesiones (
                user_id, rutina_id, fecha_sesion, hora_inicio, hora_fin, 
                duracion_minutos, calorias_quemadas, ejercicios_completados, 
                estado, notas, rendimiento, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $user->id,
            $validated['rutina_id'] ?? null,
            $validated['fecha_sesion'],
            $validated['hora_inicio'] ?? null,
            $validated['hora_fin'] ?? null,
            $validated['duracion_minutos'] ?? null,
            $validated['calorias_quemadas'] ?? null,
            $ejerciciosCompletados,
            $validated['estado'],
            $validated['notas'] ?? null,
            $validated['rendimiento'] ?? null,
            now(),
            now()
        ]);

        $sesionId = DB::getPdo()->lastInsertId();

        return redirect()->route('sesiones.show', $sesionId)
            ->with('success', 'Sesión creada exitosamente.');
    }

    /**
     * Muestra el formulario para editar una sesión
     */
    public function edit($sesion)
    {
        /** @var User $user */
        $user = Auth::user();

        $sesionData = DB::selectOne("SELECT * FROM sesiones WHERE id = ? AND user_id = ?", [$sesion, $user->id]);

        if (!$sesionData) {
            abort(404, 'Sesión no encontrada');
        }

        // Obtener rutinas del usuario
        $rutinas = DB::select("
            SELECT DISTINCT r.id, r.nombre, r.descripcion
            FROM rutina_usuario_progreso rup
            INNER JOIN rutinas r ON rup.rutina_id = r.id
            WHERE rup.user_id = ? AND r.activo = 1
        ", [$user->id]);

        return view('sesiones.edit', compact('sesionData', 'rutinas'));
    }

    /**
     * Actualiza una sesión
     */
    public function update(Request $request, $sesion)
    {
        $validated = $request->validate([
            'rutina_id' => 'nullable|exists:rutinas,id',
            'fecha_sesion' => 'required|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'duracion_minutos' => 'nullable|integer|min:1',
            'calorias_quemadas' => 'nullable|integer|min:0',
            'ejercicios_completados' => 'nullable|integer|min:0',
            'estado' => 'required|in:en_progreso,completada,cancelada',
            'notas' => 'nullable|string|max:1000',
            'rendimiento' => 'nullable|string|max:500',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Verificar que la sesión pertenece al usuario
        $sesionData = DB::selectOne("SELECT * FROM sesiones WHERE id = ? AND user_id = ?", [$sesion, $user->id]);

        if (!$sesionData) {
            abort(404, 'Sesión no encontrada');
        }

        // Calcular duración si se proporcionan horas
        if ($request->filled('hora_inicio') && $request->filled('hora_fin')) {
            $inicio = \Carbon\Carbon::parse($request->fecha_sesion . ' ' . $request->hora_inicio);
            $fin = \Carbon\Carbon::parse($request->fecha_sesion . ' ' . $request->hora_fin);
            $duracion = $inicio->diffInMinutes($fin);
            $validated['duracion_minutos'] = $duracion;
        }

        DB::update("
            UPDATE sesiones SET 
                rutina_id = ?, fecha_sesion = ?, hora_inicio = ?, hora_fin = ?,
                duracion_minutos = ?, calorias_quemadas = ?, ejercicios_completados = ?,
                estado = ?, notas = ?, rendimiento = ?, updated_at = ?
            WHERE id = ?
        ", [
            $validated['rutina_id'] ?? null,
            $validated['fecha_sesion'],
            $validated['hora_inicio'] ?? null,
            $validated['hora_fin'] ?? null,
            $validated['duracion_minutos'] ?? null,
            $validated['calorias_quemadas'] ?? null,
            $validated['ejercicios_completados'] ?? null,
            $validated['estado'],
            $validated['notas'] ?? null,
            $validated['rendimiento'] ?? null,
            now(),
            $sesion
        ]);

        return redirect()->route('sesiones.show', $sesion)
            ->with('success', 'Sesión actualizada exitosamente.');
    }

    /**
     * Completa una sesión (cambia el estado a completada)
     */
    public function complete($sesion)
    {
        /** @var User $user */
        $user = Auth::user();

        $sesionData = DB::selectOne("SELECT * FROM sesiones WHERE id = ? AND user_id = ?", [$sesion, $user->id]);

        if (!$sesionData) {
            abort(404, 'Sesión no encontrada');
        }

        // Solo se puede completar si está en progreso
        if ($sesionData->estado !== 'en_progreso') {
            return redirect()->route('sesiones.show', $sesion)
                ->with('error', 'Solo se pueden completar sesiones en progreso.');
        }

        // Actualizar estado y hora fin si no existe
        $horaFin = $sesionData->hora_fin ?: now()->toTimeString();
        $duracionMinutos = $sesionData->duracion_minutos;

        // Calcular duración si no existe y tenemos horas
        if (!$duracionMinutos && $sesionData->hora_inicio) {
            $horaInicio = \Carbon\Carbon::parse($sesionData->fecha_sesion . ' ' . $sesionData->hora_inicio);
            $horaFinObj = \Carbon\Carbon::parse($sesionData->fecha_sesion . ' ' . $horaFin);
            $duracionMinutos = $horaInicio->diffInMinutes($horaFinObj);
        }

        DB::update("
            UPDATE sesiones SET 
                estado = 'completada',
                hora_fin = ?,
                duracion_minutos = ?,
                updated_at = ?
            WHERE id = ?
        ", [$horaFin, $duracionMinutos, now(), $sesion]);

        // Verificar y asignar logros automáticamente
        $deportista = DB::selectOne("SELECT * FROM deportistas WHERE user_id = ? LIMIT 1", [$user->id]);
        
        if ($deportista) {
            \App\Http\Controllers\ProgresoController::verificarYAsignarLogros($user->id, $deportista->id);
        }

        return redirect()->route('sesiones.show', $sesion)
            ->with('success', '¡Sesión completada exitosamente! ¡Excelente trabajo!');
    }

    /**
     * Elimina una sesión
     */
    public function destroy($sesion)
    {
        /** @var User $user */
        $user = Auth::user();

        $sesionData = DB::selectOne("SELECT * FROM sesiones WHERE id = ? AND user_id = ?", [$sesion, $user->id]);

        if (!$sesionData) {
            abort(404, 'Sesión no encontrada');
        }

        // Eliminar ejercicios de la sesión (cascade)
        DB::delete("DELETE FROM sesion_ejercicios WHERE sesion_id = ?", [$sesion]);

        // Eliminar sesión
        DB::delete("DELETE FROM sesiones WHERE id = ?", [$sesion]);

        return redirect()->route('sesiones.index')
            ->with('success', 'Sesión eliminada exitosamente.');
    }

    /**
     * Crea una sesión automáticamente cuando se completa una rutina
     * Este método es llamado desde RutinaController cuando se finaliza una rutina
     */
    public static function crearDesdeRutina($userId, $rutinaId, $fechaSesion = null)
    {
        $fechaSesion = $fechaSesion ?? now()->toDateString();

        // Obtener información de la rutina
        $rutina = DB::selectOne("SELECT * FROM rutinas WHERE id = ? LIMIT 1", [$rutinaId]);
        if (!$rutina) {
            return null;
        }

        // Contar ejercicios completados hoy
        $deportista = DB::selectOne("SELECT * FROM deportistas WHERE user_id = ? LIMIT 1", [$userId]);
        if (!$deportista) {
            return null;
        }

        $ejerciciosCompletados = DB::selectOne("
            SELECT COUNT(*) as total
            FROM progresos p
            INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
            WHERE p.deportista_id = ?
            AND dr.rutina_id = ?
            AND p.fecha_registro = ?
            AND p.porcentaje_completado = 100
        ", [$deportista->id, $rutinaId, $fechaSesion])->total ?? 0;

        // Crear sesión
        DB::insert("
            INSERT INTO sesiones (
                user_id, rutina_id, fecha_sesion, hora_inicio, hora_fin,
                duracion_minutos, calorias_quemadas, ejercicios_completados,
                estado, notas, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $userId,
            $rutinaId,
            $fechaSesion,
            now()->toTimeString(),
            now()->toTimeString(),
            $rutina->tiempo_estimado_minutos ?? null,
            $rutina->calorias_estimadas ?? null,
            $ejerciciosCompletados,
            'completada',
            "Rutina completada: {$rutina->nombre}",
            now(),
            now()
        ]);

        $sesionId = DB::getPdo()->lastInsertId();

        // Agregar ejercicios completados a la sesión
        $ejerciciosProgreso = DB::select("
            SELECT dr.ejercicio_id, dr.id as detalle_rutina_id
            FROM progresos p
            INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
            WHERE p.deportista_id = ?
            AND dr.rutina_id = ?
            AND p.fecha_registro = ?
            AND p.porcentaje_completado = 100
        ", [$deportista->id, $rutinaId, $fechaSesion]);

        foreach ($ejerciciosProgreso as $ejercicio) {
            DB::insert("
                INSERT INTO sesion_ejercicios (sesion_id, ejercicio_id, detalle_rutina_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?)
            ", [$sesionId, $ejercicio->ejercicio_id, $ejercicio->detalle_rutina_id, now(), now()]);
        }

        return $sesionId;
    }
}
