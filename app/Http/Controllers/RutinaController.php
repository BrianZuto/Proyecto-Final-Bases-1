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
        $plan = DB::table('plan_usuario')
            ->join('planes', 'plan_usuario.plan_id', '=', 'planes.id')
            ->where('plan_usuario.user_id', $userId)
            ->where('plan_usuario.activo', true)
            ->where('plan_usuario.fecha_fin', '>=', now()->toDateString())
            ->select('planes.*', 'plan_usuario.fecha_inicio', 'plan_usuario.fecha_fin', 'plan_usuario.activo')
            ->first();

        return $plan;
    }

    /**
     * Calcula las métricas de una rutina usando SQL
     */
    private function calcularMetricas($rutinaId)
    {
        // Calcular tiempo total (suma de duración de ejercicios + tiempo de descanso)
        $tiempoTotal = DB::table('rutina_ejercicio')
            ->join('ejercicios', 'rutina_ejercicio.ejercicio_id', '=', 'ejercicios.id')
            ->where('rutina_ejercicio.rutina_id', $rutinaId)
            ->sum(DB::raw('COALESCE(ejercicios.duracion_minutos, 0)'));

        // Calcular tiempo de descanso
        $tiempoDescanso = DB::table('rutina_ejercicio')
            ->where('rutina_id', $rutinaId)
            ->get()
            ->sum(function($item) {
                $series = $item->series ?? 1;
                $descanso = $item->descanso_segundos ?? 0;
                return (($series - 1) * $descanso) / 60; // Convertir segundos a minutos
            });

        // Calcular calorías totales
        $caloriasTotal = DB::table('rutina_ejercicio')
            ->join('ejercicios', 'rutina_ejercicio.ejercicio_id', '=', 'ejercicios.id')
            ->where('rutina_ejercicio.rutina_id', $rutinaId)
            ->sum(DB::raw('COALESCE(ejercicios.calorias_estimadas, 0)'));

        // Actualizar rutina
        DB::table('rutinas')
            ->where('id', $rutinaId)
            ->update([
                'tiempo_estimado_minutos' => (int) ($tiempoTotal + $tiempoDescanso),
                'calorias_estimadas' => (int) $caloriasTotal,
                'updated_at' => now(),
            ]);
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

        // Construir query base
        $query = DB::table('rutinas')
            ->leftJoin('tipo_rutinas', 'rutinas.tipo_rutina_id', '=', 'tipo_rutinas.id')
            ->where('rutinas.activo', true);

        // Si no es Administrador, filtrar por plan
        if (!$user->isAdministrador() && $planActivo) {
            $rutinasPlanIds = DB::table('rutina_plan')
                ->where('plan_id', $planActivo->id)
                ->pluck('rutina_id')
                ->toArray();

            if (empty($rutinasPlanIds)) {
                $rutinasPlanIds = [0]; // Forzar resultado vacío
            }

            $query->whereIn('rutinas.id', $rutinasPlanIds);
        }

        // Filtro por búsqueda
        if ($request->filled('buscar')) {
            $buscar = '%' . $request->buscar . '%';
            $query->where(function($q) use ($buscar) {
                $q->where('rutinas.nombre', 'LIKE', $buscar)
                  ->orWhere('rutinas.descripcion', 'LIKE', $buscar);
            });
        }

        // Filtro por tipo de rutina
        if ($request->filled('tipo_rutina_id') && $request->tipo_rutina_id !== 'Todos') {
            $query->where('rutinas.tipo_rutina_id', $request->tipo_rutina_id);
        }

        // Filtro por nivel
        if ($request->filled('nivel') && $request->nivel !== 'Todos') {
            $query->where('rutinas.nivel', $request->nivel);
        }

        // Seleccionar campos
        $query->select(
            'rutinas.*',
            'tipo_rutinas.nombre as tipo_nombre',
            'tipo_rutinas.color as tipo_color'
        );

        // Paginación manual
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $total = $query->count();
        $rutinasData = $query->orderBy('rutinas.nombre')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        // Cargar ejercicios para cada rutina
        $rutinasIds = $rutinasData->pluck('id')->toArray();
        $ejerciciosPorRutina = [];

        if (!empty($rutinasIds)) {
            $ejercicios = DB::table('rutina_ejercicio')
                ->join('ejercicios', 'rutina_ejercicio.ejercicio_id', '=', 'ejercicios.id')
                ->leftJoin('categorias', 'ejercicios.categoria_id', '=', 'categorias.id')
                ->whereIn('rutina_ejercicio.rutina_id', $rutinasIds)
                ->select(
                    'rutina_ejercicio.rutina_id',
                    'ejercicios.id as ejercicio_id',
                    'ejercicios.nombre as ejercicio_nombre',
                    'categorias.nombre as categoria_nombre',
                    'rutina_ejercicio.orden',
                    'rutina_ejercicio.series',
                    'rutina_ejercicio.repeticiones',
                    'rutina_ejercicio.peso',
                    'rutina_ejercicio.descanso_segundos',
                    'rutina_ejercicio.notas'
                )
                ->orderBy('rutina_ejercicio.orden')
                ->get()
                ->groupBy('rutina_id');

            $ejerciciosPorRutina = $ejercicios->toArray();
        }

        // Cargar progreso si no es Administrador
        $progresoPorRutina = [];
        if (!$user->isAdministrador() && !empty($rutinasIds)) {
            $progresos = DB::table('rutina_usuario_progreso')
                ->where('user_id', $user->id)
                ->whereIn('rutina_id', $rutinasIds)
                ->get()
                ->keyBy('rutina_id');

            foreach ($progresos as $progreso) {
                $progresoPorRutina[$progreso->rutina_id] = $progreso->porcentaje_completado;
            }
        }

        // Transformar datos para la vista
        $rutinas = $rutinasData->map(function($rutina) use ($ejerciciosPorRutina, $progresoPorRutina) {
            // Obtener ejercicios de esta rutina
            $ejercicios = isset($ejerciciosPorRutina[$rutina->id])
                ? collect($ejerciciosPorRutina[$rutina->id])
                : collect([]);

            $rutina->ejercicios = $ejercicios;
            $rutina->progreso = $progresoPorRutina[$rutina->id] ?? 0;

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
        $tipoRutinasQuery = DB::table('tipo_rutinas')
            ->where('activo', true);

        if (!$user->isAdministrador()) {
            $tipoRutinasIds = $rutinasData->pluck('tipo_rutina_id')->filter()->unique()->toArray();
            if (!empty($tipoRutinasIds)) {
                $tipoRutinasQuery->whereIn('id', $tipoRutinasIds);
            } else {
                $tipoRutinasQuery->whereRaw('1 = 0'); // Forzar resultado vacío
            }
        }

        $tipoRutinas = $tipoRutinasQuery->orderBy('nombre')->get();

        return view('rutinas.index', compact('rutinas', 'tipoRutinas', 'planActivo'));
    }

    /**
     * Muestra el formulario para crear una nueva rutina
     */
    public function create()
    {
        $tipoRutinas = DB::table('tipo_rutinas')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $planes = DB::table('planes')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $ejercicios = DB::table('ejercicios')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

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
            $rutinaId = DB::table('rutinas')->insertGetId([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo_rutina_id' => $request->tipo_rutina_id,
                'nivel' => $request->nivel,
                'imagen_url' => $request->imagen_url,
                'activo' => $request->has('activo') ? true : false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insertar ejercicios
            $ejerciciosData = [];
            foreach ($request->ejercicios as $ejercicioData) {
                $ejerciciosData[] = [
                    'rutina_id' => $rutinaId,
                    'ejercicio_id' => $ejercicioData['ejercicio_id'],
                    'orden' => $ejercicioData['orden'],
                    'series' => $ejercicioData['series'] ?? null,
                    'repeticiones' => $ejercicioData['repeticiones'] ?? null,
                    'peso' => $ejercicioData['peso'] ?? null,
                    'descanso_segundos' => $ejercicioData['descanso_segundos'] ?? null,
                    'notas' => $ejercicioData['notas'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('rutina_ejercicio')->insert($ejerciciosData);

            // Calcular métricas
            $this->calcularMetricas($rutinaId);

            // Asignar planes
            if ($request->filled('planes')) {
                $planesData = [];
                foreach ($request->planes as $planId) {
                    $planesData[] = [
                        'rutina_id' => $rutinaId,
                        'plan_id' => $planId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('rutina_plan')->insert($planesData);
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
        $rutinaData = DB::table('rutinas')
            ->leftJoin('tipo_rutinas', 'rutinas.tipo_rutina_id', '=', 'tipo_rutinas.id')
            ->where('rutinas.id', $rutina)
            ->select(
                'rutinas.*',
                'tipo_rutinas.nombre as tipo_nombre',
                'tipo_rutinas.color as tipo_color',
                'tipo_rutinas.descripcion as tipo_descripcion'
            )
            ->first();

        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        // Cargar ejercicios
        $ejercicios = DB::table('rutina_ejercicio')
            ->join('ejercicios', 'rutina_ejercicio.ejercicio_id', '=', 'ejercicios.id')
            ->leftJoin('categorias', 'ejercicios.categoria_id', '=', 'categorias.id')
            ->where('rutina_ejercicio.rutina_id', $rutina)
            ->select(
                'ejercicios.*',
                'categorias.nombre as categoria_nombre',
                'rutina_ejercicio.orden',
                'rutina_ejercicio.series',
                'rutina_ejercicio.repeticiones',
                'rutina_ejercicio.peso',
                'rutina_ejercicio.descanso_segundos',
                'rutina_ejercicio.notas'
            )
            ->orderBy('rutina_ejercicio.orden')
            ->get();

        // Agregar pivot simulado a cada ejercicio
        $ejercicios = $ejercicios->map(function($ejercicio) {
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
        $progreso = null;

        if (!$user->isAdministrador()) {
            $progreso = DB::table('rutina_usuario_progreso')
                ->where('user_id', $user->id)
                ->where('rutina_id', $rutina)
                ->first();
        }

        return view('rutinas.show', ['rutina' => $rutinaData, 'progreso' => $progreso]);
    }

    /**
     * Muestra el formulario para editar una rutina
     */
    public function edit($rutina)
    {
        $rutinaData = DB::table('rutinas')
            ->where('id', $rutina)
            ->first();

        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        $tipoRutinas = DB::table('tipo_rutinas')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $planes = DB::table('planes')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $planesAsignados = DB::table('rutina_plan')
            ->where('rutina_id', $rutina)
            ->pluck('plan_id')
            ->toArray();

        $ejercicios = DB::table('ejercicios')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $ejerciciosAsignados = DB::table('rutina_ejercicio')
            ->where('rutina_id', $rutina)
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->ejercicio_id => [
                    'orden' => $item->orden,
                    'series' => $item->series,
                    'repeticiones' => $item->repeticiones,
                    'peso' => $item->peso,
                    'descanso_segundos' => $item->descanso_segundos,
                    'notas' => $item->notas,
                ]];
            })
            ->toArray();

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
        $rutinaData = DB::table('rutinas')->where('id', $rutina)->first();
        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        DB::beginTransaction();
        try {
            // Actualizar rutina
            DB::table('rutinas')
                ->where('id', $rutina)
                ->update([
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'tipo_rutina_id' => $request->tipo_rutina_id,
                    'nivel' => $request->nivel,
                    'imagen_url' => $request->imagen_url,
                    'activo' => $request->has('activo') ? true : false,
                    'updated_at' => now(),
                ]);

            // Eliminar ejercicios actuales
            DB::table('rutina_ejercicio')->where('rutina_id', $rutina)->delete();

            // Insertar nuevos ejercicios
            $ejerciciosData = [];
            foreach ($request->ejercicios as $ejercicioData) {
                $ejerciciosData[] = [
                    'rutina_id' => $rutina,
                    'ejercicio_id' => $ejercicioData['ejercicio_id'],
                    'orden' => $ejercicioData['orden'],
                    'series' => $ejercicioData['series'] ?? null,
                    'repeticiones' => $ejercicioData['repeticiones'] ?? null,
                    'peso' => $ejercicioData['peso'] ?? null,
                    'descanso_segundos' => $ejercicioData['descanso_segundos'] ?? null,
                    'notas' => $ejercicioData['notas'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('rutina_ejercicio')->insert($ejerciciosData);

            // Recalcular métricas
            $this->calcularMetricas($rutina);

            // Actualizar planes
            DB::table('rutina_plan')->where('rutina_id', $rutina)->delete();
            if ($request->filled('planes')) {
                $planesData = [];
                foreach ($request->planes as $planId) {
                    $planesData[] = [
                        'rutina_id' => $rutina,
                        'plan_id' => $planId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('rutina_plan')->insert($planesData);
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
        $rutinaData = DB::table('rutinas')->where('id', $rutina)->first();
        if (!$rutinaData) {
            abort(404, 'Rutina no encontrada');
        }

        // Verificar si hay usuarios con progreso
        $progresoCount = DB::table('rutina_usuario_progreso')
            ->where('rutina_id', $rutina)
            ->count();

        if ($progresoCount > 0) {
            return redirect()->route('rutinas.index')->with('error', 'No se puede eliminar la rutina porque hay usuarios con progreso en ella.');
        }

        DB::beginTransaction();
        try {
            // Eliminar relaciones
            DB::table('rutina_ejercicio')->where('rutina_id', $rutina)->delete();
            DB::table('rutina_plan')->where('rutina_id', $rutina)->delete();

            // Eliminar rutina
            DB::table('rutinas')->where('id', $rutina)->delete();

            DB::commit();
            return redirect()->route('rutinas.index')->with('success', 'Rutina eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('rutinas.index')->with('error', 'Error al eliminar la rutina: ' . $e->getMessage());
        }
    }
}
