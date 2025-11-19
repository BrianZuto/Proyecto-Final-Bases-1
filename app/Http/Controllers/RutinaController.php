<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RutinaController extends Controller
{
    /**
     * Obtiene el plan activo del usuario usando SQL
     */
    private function obtenerPlanActivo($userId)
    {
        $plan = DB::selectOne("
            SELECT
                p.*,
                pu.fecha_inicio,
                pu.fecha_fin,
                pu.activo
            FROM plan_usuario pu
            INNER JOIN planes p ON pu.plan_id = p.id
            WHERE pu.user_id = ?
            AND pu.activo = 1
            AND pu.fecha_fin >= ?
        ", [$userId, now()->toDateString()]);

        return $plan;
    }

    /**
     * Calcula las métricas de una rutina usando SQL
     */
    private function calcularMetricas($rutinaId)
    {
        // Calcular tiempo total (suma de duración de ejercicios + tiempo de descanso)
        $tiempoTotal = DB::selectOne("
            SELECT COALESCE(SUM(COALESCE(e.duracion_minutos, 0)), 0) as total
            FROM detalle_rutinas dr
            INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
            WHERE dr.rutina_id = ?
        ", [$rutinaId])->total ?? 0;

        // Calcular tiempo de descanso
        $detalles = DB::select("SELECT series, descanso_segundos FROM detalle_rutinas WHERE rutina_id = ?", [$rutinaId]);
        $tiempoDescanso = collect($detalles)->sum(function($item) {
            $series = $item->series ?? 1;
            $descanso = $item->descanso_segundos ?? 0;
            return (($series - 1) * $descanso) / 60; // Convertir segundos a minutos
        });

        // Calcular calorías totales
        $caloriasTotal = DB::selectOne("
            SELECT COALESCE(SUM(COALESCE(e.calorias_estimadas, 0)), 0) as total
            FROM detalle_rutinas dr
            INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
            WHERE dr.rutina_id = ?
        ", [$rutinaId])->total ?? 0;

        // Actualizar rutina
        DB::update("
            UPDATE rutinas SET
                tiempo_estimado_minutos = ?,
                calorias_estimadas = ?,
                updated_at = ?
            WHERE id = ?
        ", [(int)($tiempoTotal + $tiempoDescanso), (int)$caloriasTotal, now(), $rutinaId]);
    }

    /**
     * Muestra la lista de rutinas
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $planActivo = $this->obtenerPlanActivo($user->id);

        // Si el usuario no tiene plan activo Y no es Administrador, no puede ver rutinas
        if (!$planActivo && !$user->isAdministrador()) {
            $rutinasVacios = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                12,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('rutinas.index', [
                'rutinas' => $rutinasVacios,
                'tipoRutinas' => collect([]),
                'planActivo' => null,
                'message' => 'No tienes un plan activo. Necesitas un plan para acceder a las rutinas.'
            ]);
        }

        // Construir query base con SQL directo
        $whereConditions = ["r.activo = 1"];
        $params = [];

        // Si no es Administrador, filtrar por plan
        if (!$user->isAdministrador() && $planActivo) {
            $rutinasPlanIds = DB::select("SELECT rutina_id FROM rutina_plan WHERE plan_id = ?", [$planActivo->id]);
            $rutinasPlanIds = collect($rutinasPlanIds)->pluck('rutina_id')->toArray();

            if (empty($rutinasPlanIds)) {
                $rutinasPlanIds = [0]; // Forzar resultado vacío
            }

            $placeholders = implode(',', array_fill(0, count($rutinasPlanIds), '?'));
            $whereConditions[] = "r.id IN ({$placeholders})";
            $params = array_merge($params, $rutinasPlanIds);
        }

        // Filtro por búsqueda
        if ($request->filled('buscar')) {
            $buscar = '%' . $request->buscar . '%';
            $whereConditions[] = "(r.nombre LIKE ? OR r.descripcion LIKE ?)";
            $params[] = $buscar;
            $params[] = $buscar;
        }

        // Filtro por tipo de rutina
        if ($request->filled('tipo_rutina_id') && $request->tipo_rutina_id !== 'Todos') {
            $whereConditions[] = "r.tipo_rutina_id = ?";
            $params[] = $request->tipo_rutina_id;
        }

        // Filtro por nivel
        if ($request->filled('nivel') && $request->nivel !== 'Todos') {
            $whereConditions[] = "r.nivel = ?";
            $params[] = $request->nivel;
        }

        $whereClause = implode(' AND ', $whereConditions);

        // Paginación manual
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        // Contar total
        $total = DB::selectOne("
            SELECT COUNT(*) as total
            FROM rutinas r
            LEFT JOIN tipo_rutinas tr ON r.tipo_rutina_id = tr.id
            WHERE {$whereClause}
        ", $params)->total ?? 0;

        // Obtener rutinas
        $rutinasData = DB::select("
            SELECT
                r.*,
                tr.nombre as tipo_nombre,
                tr.color as tipo_color
            FROM rutinas r
            LEFT JOIN tipo_rutinas tr ON r.tipo_rutina_id = tr.id
            WHERE {$whereClause}
            ORDER BY r.nombre
            LIMIT ? OFFSET ?
        ", array_merge($params, [$perPage, $offset]));

        // Cargar ejercicios para cada rutina
        $rutinasIds = collect($rutinasData)->pluck('id')->toArray();
        $ejerciciosPorRutina = [];

        if (!empty($rutinasIds)) {
            $placeholders = implode(',', array_fill(0, count($rutinasIds), '?'));
            $ejercicios = DB::select("
                SELECT
                    dr.rutina_id,
                    e.id as ejercicio_id,
                    e.nombre as ejercicio_nombre,
                    c.nombre as categoria_nombre,
                    dr.orden,
                    dr.series,
                    dr.repeticiones,
                    dr.peso,
                    dr.descanso_segundos,
                    dr.notas
                FROM detalle_rutinas dr
                INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE dr.rutina_id IN ({$placeholders})
                ORDER BY dr.orden
            ", $rutinasIds);

            $ejerciciosPorRutina = collect($ejercicios)->groupBy('rutina_id')->toArray();
        }

        // Cargar progreso para todos los usuarios
        $progresoPorRutina = [];
        if (!empty($rutinasIds)) {
            $placeholders = implode(',', array_fill(0, count($rutinasIds), '?'));
            $progresos = DB::select("
                SELECT * FROM rutina_usuario_progreso
                WHERE user_id = ? AND rutina_id IN ({$placeholders})
            ", array_merge([$user->id], $rutinasIds));

            foreach ($progresos as $progreso) {
                $progresoPorRutina[$progreso->rutina_id] = $progreso;
            }
        }

        // Transformar datos para la vista
        $rutinas = collect($rutinasData)->map(function($rutina) use ($ejerciciosPorRutina, $progresoPorRutina) {
            // Obtener ejercicios de esta rutina
            $ejercicios = isset($ejerciciosPorRutina[$rutina->id])
                ? collect($ejerciciosPorRutina[$rutina->id])
                : collect([]);

            $rutina->ejercicios = $ejercicios;
            $progresoData = $progresoPorRutina[$rutina->id] ?? null;
            $rutina->progreso = $progresoData ? $progresoData->porcentaje_completado : 0;
            $rutina->progresoData = $progresoData;

            // Crear objeto tipoRutina simulado
            if ($rutina->tipo_rutina_id) {
                $rutina->tipoRutina = (object) [
                    'id' => $rutina->tipo_rutina_id,
                    'nombre' => $rutina->tipo_nombre,
                    'color' => $rutina->tipo_color,
                ];
            }

            // Agregar color_nivel (accessor del modelo ahora manual)
            $rutina->color_nivel = match($rutina->nivel) {
                'Principiante' => 'bg-green-100 text-green-700',
                'Intermedio' => 'bg-yellow-100 text-yellow-700',
                'Avanzado' => 'bg-pink-100 text-pink-700',
                default => 'bg-gray-100 text-gray-700',
            };

            return $rutina;
        });

        // Crear paginador
        $rutinas = new \Illuminate\Pagination\LengthAwarePaginator(
            $rutinas,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Obtener tipos de rutinas
        if (!$user->isAdministrador()) {
            $tipoRutinasIds = collect($rutinasData)->pluck('tipo_rutina_id')->filter()->unique()->toArray();
            if (!empty($tipoRutinasIds)) {
                $placeholders = implode(',', array_fill(0, count($tipoRutinasIds), '?'));
                $tipoRutinas = DB::select("
                    SELECT * FROM tipo_rutinas
                    WHERE activo = 1 AND id IN ({$placeholders})
                    ORDER BY nombre
                ", $tipoRutinasIds);
            } else {
                $tipoRutinas = [];
            }
        } else {
            $tipoRutinas = DB::select("SELECT * FROM tipo_rutinas WHERE activo = 1 ORDER BY nombre");
        }

        return view('rutinas.index', compact('rutinas', 'tipoRutinas', 'planActivo'));
    }

    /**
     * Muestra el formulario para crear una nueva rutina
     */
    public function create()
    {
        $tipoRutinas = DB::select("SELECT * FROM tipo_rutinas WHERE activo = 1 ORDER BY nombre");
        $planes = DB::select("SELECT * FROM planes WHERE activo = 1 ORDER BY nombre");
        $ejercicios = DB::select("SELECT * FROM ejercicios WHERE activo = 1 ORDER BY nombre");

        return view('rutinas.create', compact('tipoRutinas', 'planes', 'ejercicios'));
    }

    /**
     * Almacena una nueva rutina
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tipo_rutina_id' => 'required|exists:tipo_rutinas,id',
            'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
            'imagen_url' => 'nullable|url|max:500',
            'activo' => 'nullable|boolean',
            'planes' => 'nullable|array',
            'planes.*' => 'exists:planes,id',
            'ejercicios' => 'required|array|min:1',
            'ejercicios.*.ejercicio_id' => 'required|exists:ejercicios,id',
            'ejercicios.*.orden' => 'required|integer|min:1',
            'ejercicios.*.series' => 'nullable|integer|min:1',
            'ejercicios.*.repeticiones' => 'nullable|string|max:255',
            'ejercicios.*.peso' => 'nullable|string|max:255',
            'ejercicios.*.descanso_segundos' => 'nullable|integer|min:0',
            'ejercicios.*.notas' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Insertar rutina
            DB::insert("
                INSERT INTO rutinas (nombre, descripcion, tipo_rutina_id, nivel, imagen_url, activo, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $request->nombre,
                $request->descripcion,
                $request->tipo_rutina_id,
                $request->nivel,
                $request->imagen_url,
                $request->has('activo') ? 1 : 0,
                now(),
                now()
            ]);

            $rutinaId = DB::getPdo()->lastInsertId();

            // Insertar ejercicios
            foreach ($request->ejercicios as $ejercicioData) {
                DB::insert("
                    INSERT INTO detalle_rutinas (
                        rutina_id, ejercicio_id, orden, series, repeticiones,
                        peso, descanso_segundos, notas, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ", [
                    $rutinaId,
                    $ejercicioData['ejercicio_id'],
                    $ejercicioData['orden'],
                    $ejercicioData['series'] ?? null,
                    $ejercicioData['repeticiones'] ?? null,
                    $ejercicioData['peso'] ?? null,
                    $ejercicioData['descanso_segundos'] ?? null,
                    $ejercicioData['notas'] ?? null,
                    now(),
                    now()
                ]);
            }

            // Calcular métricas
            $this->calcularMetricas($rutinaId);

            // Asignar planes
            if ($request->filled('planes')) {
                foreach ($request->planes as $planId) {
                    DB::insert("
                        INSERT INTO rutina_plan (rutina_id, plan_id, created_at, updated_at)
                        VALUES (?, ?, ?, ?)
                    ", [$rutinaId, $planId, now(), now()]);
                }
            }

            DB::commit();
            return redirect()->route('rutinas.index')->with('success', 'Rutina creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al crear la rutina: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de una rutina
     */
    public function show($rutina)
    {
        $rutinaData = DB::selectOne("
            SELECT
                r.*,
                tr.nombre as tipo_nombre,
                tr.color as tipo_color,
                tr.descripcion as tipo_descripcion
            FROM rutinas r
            LEFT JOIN tipo_rutinas tr ON r.tipo_rutina_id = tr.id
            WHERE r.id = ?
        ", [$rutina]);

        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        // Cargar ejercicios
        $ejercicios = DB::select("
            SELECT
                e.*,
                c.nombre as categoria_nombre,
                dr.orden,
                dr.series,
                dr.repeticiones,
                dr.peso,
                dr.descanso_segundos,
                dr.notas
            FROM detalle_rutinas dr
            INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE dr.rutina_id = ?
            ORDER BY dr.orden
        ", [$rutina]);

        // Agregar pivot simulado a cada ejercicio
        $ejercicios = collect($ejercicios)->map(function($ejercicio) {
            $ejercicio->pivot = (object) [
                'orden' => $ejercicio->orden,
                'series' => $ejercicio->series,
                'repeticiones' => $ejercicio->repeticiones,
                'peso' => $ejercicio->peso,
                'descanso_segundos' => $ejercicio->descanso_segundos,
                'notas' => $ejercicio->notas,
            ];
            $ejercicio->categoria = $ejercicio->categoria_nombre ? (object) ['nombre' => $ejercicio->categoria_nombre] : null;
            return $ejercicio;
        });

        // Crear objeto tipoRutina
        if ($rutinaData->tipo_rutina_id) {
            $rutinaData->tipoRutina = (object) [
                'id' => $rutinaData->tipo_rutina_id,
                'nombre' => $rutinaData->tipo_nombre,
                'color' => $rutinaData->tipo_color,
                'descripcion' => $rutinaData->tipo_descripcion,
            ];
        }

        // Agregar color_nivel (accessor del modelo ahora manual)
        $rutinaData->color_nivel = match($rutinaData->nivel) {
            'Principiante' => 'bg-green-100 text-green-700',
            'Intermedio' => 'bg-yellow-100 text-yellow-700',
            'Avanzado' => 'bg-pink-100 text-pink-700',
            default => 'bg-gray-100 text-gray-700',
        };

        $rutinaData->ejercicios = $ejercicios;

        /** @var User $user */
        $user = Auth::user();

        // Cargar progreso para todos los usuarios
        $progreso = DB::selectOne("
            SELECT * FROM rutina_usuario_progreso
            WHERE user_id = ? AND rutina_id = ?
        ", [$user->id, $rutina]);

        return view('rutinas.show', ['rutina' => $rutinaData, 'progreso' => $progreso]);
    }

    /**
     * Muestra el formulario para editar una rutina
     */
    public function edit($rutina)
    {
        $rutinaData = DB::selectOne("SELECT * FROM rutinas WHERE id = ?", [$rutina]);

        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        $tipoRutinas = DB::select("SELECT * FROM tipo_rutinas WHERE activo = 1 ORDER BY nombre");
        $planes = DB::select("SELECT * FROM planes WHERE activo = 1 ORDER BY nombre");

        $planesAsignadosData = DB::select("SELECT plan_id FROM rutina_plan WHERE rutina_id = ?", [$rutina]);
        $planesAsignados = collect($planesAsignadosData)->pluck('plan_id')->toArray();

        $ejercicios = DB::select("SELECT * FROM ejercicios WHERE activo = 1 ORDER BY nombre");

        $ejerciciosAsignadosData = DB::select("SELECT * FROM detalle_rutinas WHERE rutina_id = ?", [$rutina]);
        $ejerciciosAsignados = collect($ejerciciosAsignadosData)->mapWithKeys(function($item) {
            return [$item->ejercicio_id => [
                'orden' => $item->orden,
                'series' => $item->series,
                'repeticiones' => $item->repeticiones,
                'peso' => $item->peso,
                'descanso_segundos' => $item->descanso_segundos,
                'notas' => $item->notas,
            ]];
        })->toArray();

        return view('rutinas.edit', [
            'rutina' => $rutinaData,
            'tipoRutinas' => $tipoRutinas,
            'planes' => $planes,
            'planesAsignados' => $planesAsignados,
            'ejercicios' => $ejercicios,
            'ejerciciosAsignados' => $ejerciciosAsignados
        ]);
    }

    /**
     * Actualiza una rutina
     */
    public function update(Request $request, $rutina)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tipo_rutina_id' => 'required|exists:tipo_rutinas,id',
            'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
            'imagen_url' => 'nullable|url|max:500',
            'activo' => 'nullable|boolean',
            'planes' => 'nullable|array',
            'planes.*' => 'exists:planes,id',
            'ejercicios' => 'required|array|min:1',
            'ejercicios.*.ejercicio_id' => 'required|exists:ejercicios,id',
            'ejercicios.*.orden' => 'required|integer|min:1',
            'ejercicios.*.series' => 'nullable|integer|min:1',
            'ejercicios.*.repeticiones' => 'nullable|string|max:255',
            'ejercicios.*.peso' => 'nullable|string|max:255',
            'ejercicios.*.descanso_segundos' => 'nullable|integer|min:0',
            'ejercicios.*.notas' => 'nullable|string',
        ]);

        // Verificar que la rutina existe
        $rutinaData = DB::selectOne("SELECT * FROM rutinas WHERE id = ?", [$rutina]);
        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        DB::beginTransaction();
        try {
            // Actualizar rutina
            DB::update("
                UPDATE rutinas SET
                    nombre = ?, descripcion = ?, tipo_rutina_id = ?, nivel = ?,
                    imagen_url = ?, activo = ?, updated_at = ?
                WHERE id = ?
            ", [
                $request->nombre,
                $request->descripcion,
                $request->tipo_rutina_id,
                $request->nivel,
                $request->imagen_url,
                $request->has('activo') ? 1 : 0,
                now(),
                $rutina
            ]);

            // Eliminar ejercicios actuales
            DB::delete("DELETE FROM detalle_rutinas WHERE rutina_id = ?", [$rutina]);

            // Insertar nuevos ejercicios
            foreach ($request->ejercicios as $ejercicioData) {
                DB::insert("
                    INSERT INTO detalle_rutinas (
                        rutina_id, ejercicio_id, orden, series, repeticiones,
                        peso, descanso_segundos, notas, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ", [
                    $rutina,
                    $ejercicioData['ejercicio_id'],
                    $ejercicioData['orden'],
                    $ejercicioData['series'] ?? null,
                    $ejercicioData['repeticiones'] ?? null,
                    $ejercicioData['peso'] ?? null,
                    $ejercicioData['descanso_segundos'] ?? null,
                    $ejercicioData['notas'] ?? null,
                    now(),
                    now()
                ]);
            }

            // Recalcular métricas
            $this->calcularMetricas($rutina);

            // Actualizar planes
            DB::delete("DELETE FROM rutina_plan WHERE rutina_id = ?", [$rutina]);
            if ($request->filled('planes')) {
                foreach ($request->planes as $planId) {
                    DB::insert("
                        INSERT INTO rutina_plan (rutina_id, plan_id, created_at, updated_at)
                        VALUES (?, ?, ?, ?)
                    ", [$rutina, $planId, now(), now()]);
                }
            }

            DB::commit();
            return redirect()->route('rutinas.index')->with('success', 'Rutina actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al actualizar la rutina: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una rutina
     */
    public function destroy($rutina)
    {
        // Verificar que la rutina existe
        $rutinaData = DB::selectOne("SELECT * FROM rutinas WHERE id = ?", [$rutina]);
        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        // Verificar si hay usuarios con progreso
        $progresoCount = DB::selectOne("
            SELECT COUNT(*) as total
            FROM rutina_usuario_progreso
            WHERE rutina_id = ?
        ", [$rutina])->total ?? 0;

        if ($progresoCount > 0) {
            return redirect()->route('rutinas.index')->with('error', 'No se puede eliminar la rutina porque hay usuarios con progreso en ella.');
        }

        DB::beginTransaction();
        try {
            // Eliminar relaciones
            DB::delete("DELETE FROM detalle_rutinas WHERE rutina_id = ?", [$rutina]);
            DB::delete("DELETE FROM rutina_plan WHERE rutina_id = ?", [$rutina]);

            // Eliminar rutina
            DB::delete("DELETE FROM rutinas WHERE id = ?", [$rutina]);

            DB::commit();
            return redirect()->route('rutinas.index')->with('success', 'Rutina eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('rutinas.index')->with('error', 'Error al eliminar la rutina: ' . $e->getMessage());
        }
    }

    /**
     * Inicia una rutina para el usuario actual
     */
    public function start($rutina)
    {
        /** @var User $user */
        $user = Auth::user();

        // Verificar que la rutina existe
        $rutinaData = DB::selectOne("SELECT * FROM rutinas WHERE id = ?", [$rutina]);
        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        // Crear o actualizar el progreso
        $progreso = DB::selectOne("
            SELECT * FROM rutina_usuario_progreso
            WHERE user_id = ? AND rutina_id = ?
        ", [$user->id, $rutina]);

        if ($progreso) {
            // Si ya existe y está completada, no permitir reiniciar
            if ($progreso->estado === 'completada') {
                return redirect()->route('rutinas.show', $rutina)
                    ->with('error', 'Esta rutina ya está completada.');
            }
            // Si ya existe, actualizar fecha de última sesión y estado
            DB::update("
                UPDATE rutina_usuario_progreso SET
                    estado = 'en_progreso',
                    fecha_ultima_sesion = ?,
                    updated_at = ?
                WHERE id = ?
            ", [now()->toDateString(), now(), $progreso->id]);
        } else {
            // Si no existe, crear nuevo registro
            DB::insert("
                INSERT INTO rutina_usuario_progreso (
                    user_id, rutina_id, porcentaje_completado, estado,
                    fecha_inicio, fecha_ultima_sesion, sesiones_completadas,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $user->id,
                $rutina,
                0.00,
                'en_progreso',
                now()->toDateString(),
                now()->toDateString(),
                0,
                now(),
                now()
            ]);
        }

        return redirect()->route('rutinas.execute', $rutina)
            ->with('success', 'Rutina iniciada. ¡Mucha suerte!');
    }

    /**
     * Muestra la vista de ejecución de la rutina
     */
    public function execute($rutina)
    {
        /** @var User $user */
        $user = Auth::user();

        // Cargar datos de la rutina
        $rutinaData = DB::selectOne("
            SELECT
                r.*,
                tr.nombre as tipo_nombre,
                tr.color as tipo_color
            FROM rutinas r
            LEFT JOIN tipo_rutinas tr ON r.tipo_rutina_id = tr.id
            WHERE r.id = ?
        ", [$rutina]);

        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        // Cargar ejercicios
        $ejercicios = DB::select("
            SELECT
                dr.id as detalle_id,
                e.*,
                c.nombre as categoria_nombre,
                dr.orden,
                dr.series,
                dr.repeticiones,
                dr.peso,
                dr.descanso_segundos,
                dr.notas
            FROM detalle_rutinas dr
            INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE dr.rutina_id = ?
            ORDER BY dr.orden
        ", [$rutina]);

        // Cargar progreso de ejercicios completados
        $progresos = [];
        $deportista = DB::selectOne("SELECT * FROM deportistas WHERE user_id = ? LIMIT 1", [$user->id]);

        if ($deportista) {
            $progresosData = DB::select("
                SELECT p.detalle_rutina_id
                FROM progresos p
                INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
                WHERE p.deportista_id = ?
                AND dr.rutina_id = ?
                AND p.fecha_registro = ?
            ", [$deportista->id, $rutina, now()->toDateString()]);
            $progresos = collect($progresosData)->pluck('detalle_rutina_id')->toArray();
        }

        // Cargar progreso general (para todos los usuarios)
        $progreso = DB::selectOne("
            SELECT * FROM rutina_usuario_progreso
            WHERE user_id = ? AND rutina_id = ?
        ", [$user->id, $rutina]);

        // Determinar estado de cada ejercicio
        $ejercicios = collect($ejercicios)->map(function($ejercicio) use ($progresos) {
            $ejercicio->completado = in_array($ejercicio->detalle_id, $progresos);
            $ejercicio->estado = $ejercicio->completado ? 'completado' : 'pendiente';
            $ejercicio->pivot = (object) [
                'orden' => $ejercicio->orden,
                'series' => $ejercicio->series,
                'repeticiones' => $ejercicio->repeticiones,
                'peso' => $ejercicio->peso,
                'descanso_segundos' => $ejercicio->descanso_segundos,
                'notas' => $ejercicio->notas,
            ];
            $ejercicio->categoria = $ejercicio->categoria_nombre ? (object) ['nombre' => $ejercicio->categoria_nombre] : null;
            return $ejercicio;
        });

        // Calcular progreso actual de la sesión
        $ejerciciosCompletados = $ejercicios->where('completado', true)->count();
        $totalEjercicios = $ejercicios->count();
        $progresoSesion = $totalEjercicios > 0 ? ($ejerciciosCompletados / $totalEjercicios) * 100 : 0;

        $rutinaData->ejercicios = $ejercicios;
        $rutinaData->tipoRutina = $rutinaData->tipo_rutina_id ? (object) [
            'id' => $rutinaData->tipo_rutina_id,
            'nombre' => $rutinaData->tipo_nombre,
            'color' => $rutinaData->tipo_color,
        ] : null;

        return view('rutinas.execute', [
            'rutina' => $rutinaData,
            'progreso' => $progreso,
            'progresoSesion' => $progresoSesion,
            'ejerciciosCompletados' => $ejerciciosCompletados,
            'totalEjercicios' => $totalEjercicios,
        ]);
    }

    /**
     * Marca ejercicios como completados (puede ser uno o varios)
     */
    public function completeExercise(Request $request, $rutina)
    {
        $request->validate([
            'ejercicios' => 'required|array|min:1',
            'ejercicios.*' => 'required|exists:detalle_rutinas,id',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Obtener o crear deportista_id
        $deportista = DB::selectOne("SELECT * FROM deportistas WHERE user_id = ? LIMIT 1", [$user->id]);

        if (!$deportista) {
            // Si no existe, crear registro en deportistas
            DB::insert("INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)", [
                $user->id,
                now(),
                now()
            ]);
            $deportistaId = DB::getPdo()->lastInsertId();
            $deportista = (object) ['id' => $deportistaId];
        }

        // Verificar que todos los ejercicios pertenecen a la rutina
        $placeholders = implode(',', array_fill(0, count($request->ejercicios), '?'));
        $detallesRutinaData = DB::select("
            SELECT id FROM detalle_rutinas
            WHERE id IN ({$placeholders}) AND rutina_id = ?
        ", array_merge($request->ejercicios, [$rutina]));
        $detallesRutina = collect($detallesRutinaData)->pluck('id')->toArray();

        if (count($detallesRutina) !== count($request->ejercicios)) {
            return response()->json(['error' => 'Algunos ejercicios no pertenecen a esta rutina.'], 404);
        }

        $completados = 0;
        $hoy = now()->toDateString();

        // Procesar cada ejercicio
        foreach ($request->ejercicios as $detalleId) {
            // Verificar si ya está completado hoy
            $progresoExistente = DB::selectOne("
                SELECT * FROM progresos
                WHERE deportista_id = ? AND detalle_rutina_id = ? AND fecha_registro = ?
            ", [$deportista->id, $detalleId, $hoy]);

            if ($progresoExistente) {
                // Si ya existe, actualizar
                DB::update("
                    UPDATE progresos SET
                        porcentaje_completado = 100.00,
                        updated_at = ?
                    WHERE id = ?
                ", [now(), $progresoExistente->id]);
            } else {
                // Si no existe, crear nuevo
                DB::insert("
                    INSERT INTO progresos (
                        deportista_id, detalle_rutina_id, fecha_registro,
                        porcentaje_completado, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?)
                ", [$deportista->id, $detalleId, $hoy, 100.00, now(), now()]);
            }
            $completados++;
        }

        // Actualizar progreso general de la rutina
        $this->actualizarProgresoRutina($user->id, $rutina);

        return response()->json([
            'success' => true,
            'message' => $completados > 1 ? "{$completados} ejercicios marcados como completados" : 'Ejercicio marcado como completado',
            'completados' => $completados,
        ]);
    }

    /**
     * Finaliza la rutina y actualiza el progreso
     */
    public function finish($rutina)
    {
        /** @var User $user */
        $user = Auth::user();

        // Actualizar progreso general
        $this->actualizarProgresoRutina($user->id, $rutina);

        // Actualizar sesiones completadas
        $progreso = DB::selectOne("
            SELECT * FROM rutina_usuario_progreso
            WHERE user_id = ? AND rutina_id = ?
        ", [$user->id, $rutina]);

        if ($progreso) {
            DB::update("
                UPDATE rutina_usuario_progreso SET
                    sesiones_completadas = sesiones_completadas + 1,
                    fecha_ultima_sesion = ?,
                    estado = 'completada',
                    updated_at = ?
                WHERE id = ?
            ", [now()->toDateString(), now(), $progreso->id]);
        }

        // Crear sesión automáticamente
        \App\Http\Controllers\SesionController::crearDesdeRutina($user->id, $rutina);

        // Verificar y asignar logros automáticamente
        $deportista = DB::selectOne("SELECT * FROM deportistas WHERE user_id = ? LIMIT 1", [$user->id]);

        if ($deportista) {
            \App\Http\Controllers\ProgresoController::verificarYAsignarLogros($user->id, $deportista->id);
        }

        return redirect()->route('rutinas.show', $rutina)
            ->with('success', '¡Rutina completada! ¡Excelente trabajo!');
    }

    /**
     * Actualiza el progreso general de la rutina
     */
    private function actualizarProgresoRutina($userId, $rutinaId)
    {
        // Obtener todos los ejercicios de la rutina
        $totalEjercicios = DB::selectOne("
            SELECT COUNT(*) as total FROM detalle_rutinas WHERE rutina_id = ?
        ", [$rutinaId])->total ?? 0;

        if ($totalEjercicios == 0) {
            return;
        }

        // Obtener o crear deportista_id
        $deportista = DB::selectOne("SELECT * FROM deportistas WHERE user_id = ? LIMIT 1", [$userId]);

        if (!$deportista) {
            // Si no existe, crear registro en deportistas
            DB::insert("INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)", [
                $userId,
                now(),
                now()
            ]);
            $deportistaId = DB::getPdo()->lastInsertId();
            $deportista = (object) ['id' => $deportistaId];
        }

        // Contar ejercicios completados (al menos una vez)
        $ejerciciosCompletados = DB::selectOne("
            SELECT COUNT(DISTINCT p.detalle_rutina_id) as total
            FROM progresos p
            INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
            WHERE p.deportista_id = ?
            AND dr.rutina_id = ?
            AND p.porcentaje_completado >= 100
        ", [$deportista->id, $rutinaId])->total ?? 0;

        // Calcular porcentaje
        $porcentaje = ($ejerciciosCompletados / $totalEjercicios) * 100;

        // Determinar estado basado en porcentaje
        $estado = 'en_progreso';
        if ($porcentaje >= 100) {
            $estado = 'completada';
        } elseif ($porcentaje == 0) {
            $estado = 'pendiente';
        }

        // Actualizar progreso
        DB::update("
            UPDATE rutina_usuario_progreso SET
                porcentaje_completado = ?,
                estado = ?,
                updated_at = ?
            WHERE user_id = ? AND rutina_id = ?
        ", [round($porcentaje, 2), $estado, now(), $userId, $rutinaId]);
    }
}
