<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EjercicioController extends Controller
{
    /**
     * Muestra la lista de ejercicios
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $planActivo = $user->planActivo();

        // Si el usuario no tiene plan activo Y no es Administrador, no puede ver ejercicios
        if (!$planActivo && !$user->isAdministrador()) {
            // Crear un paginador vacío para evitar errores en la vista
            $ejerciciosVacios = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                12,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('ejercicios.index', [
                'ejercicios' => $ejerciciosVacios,
                'categorias' => collect([]),
                'planActivo' => null,
                'message' => 'No tienes un plan activo. Necesitas un plan para acceder a los ejercicios.'
            ]);
        }

        // Si el usuario es Administrador, puede ver todos los ejercicios
        // Si no, solo los ejercicios asignados a su plan
        $query = DB::table('ejercicios')
            ->leftJoin('categorias', 'ejercicios.categoria_id', '=', 'categorias.id')
            ->where('ejercicios.activo', true);

        if (!$user->isAdministrador()) {
            // Obtener IDs de ejercicios asignados al plan del usuario usando SQL
            $ejerciciosPlanIds = DB::table('plan_ejercicio')
                ->where('plan_id', $planActivo->id)
                ->pluck('ejercicio_id')
                ->toArray();
            
            if (empty($ejerciciosPlanIds)) {
                // Si no hay ejercicios asignados, devolver lista vacía
                $ejerciciosPlanIds = [0]; // Forzar resultado vacío
            }
            $query->whereIn('ejercicios.id', $ejerciciosPlanIds);
        }

        // Filtro por búsqueda (nombre)
        if ($request->filled('buscar')) {
            $buscar = '%' . $request->buscar . '%';
            $query->where(function($q) use ($buscar) {
                $q->where('ejercicios.nombre', 'LIKE', $buscar)
                  ->orWhere('ejercicios.descripcion', 'LIKE', $buscar)
                  ->orWhere('ejercicios.grupo_muscular', 'LIKE', $buscar);
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria_id') && $request->categoria_id !== 'Todos') {
            $query->where('ejercicios.categoria_id', $request->categoria_id);
        }

        // Filtro por dificultad (nivel)
        if ($request->filled('dificultad') && $request->dificultad !== 'Todos') {
            $query->where('ejercicios.dificultad', $request->dificultad);
        }

        // Seleccionar campos
        $query->select(
            'ejercicios.*',
            'categorias.nombre as categoria_nombre',
            'categorias.color as categoria_color'
        );

        // Paginación manual
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $total = $query->count();
        $ejerciciosData = $query->orderBy('ejercicios.nombre')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        // Transformar datos para agregar categoría como objeto
        $ejercicios = $ejerciciosData->map(function($ejercicio) {
            if ($ejercicio->categoria_id && $ejercicio->categoria_nombre) {
                $ejercicio->categoria = (object) [
                    'id' => $ejercicio->categoria_id,
                    'nombre' => $ejercicio->categoria_nombre,
                    'color' => $ejercicio->categoria_color,
                ];
            }
            // Agregar color_dificultad (accessor del modelo ahora manual)
            $ejercicio->color_dificultad = match($ejercicio->dificultad) {
                'Principiante' => 'bg-green-100 text-green-700',
                'Intermedio' => 'bg-yellow-100 text-yellow-700',
                'Avanzado' => 'bg-pink-100 text-pink-700',
                default => 'bg-gray-100 text-gray-700',
            };
            return $ejercicio;
        });

        // Crear paginador
        $ejercicios = new \Illuminate\Pagination\LengthAwarePaginator(
            $ejercicios,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Obtener categorías
        $categoriasQuery = DB::table('categorias')
            ->where('activo', true);

        if (!$user->isAdministrador()) {
            $categoriasIds = $ejerciciosData->pluck('categoria_id')->filter()->unique()->toArray();
            if (!empty($categoriasIds)) {
                $categoriasQuery->whereIn('id', $categoriasIds);
            } else {
                $categoriasQuery->whereRaw('1 = 0'); // Forzar resultado vacío
            }
        }

        $categorias = $categoriasQuery->orderBy('nombre')->get();

        return view('ejercicios.index', compact('ejercicios', 'categorias', 'planActivo'));
    }

    /**
     * Muestra el formulario para crear un nuevo ejercicio
     */
    public function create()
    {
        $categorias = DB::table('categorias')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $planes = DB::table('planes')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('ejercicios.create', compact('categorias', 'planes'));
    }

    /**
     * Almacena un nuevo ejercicio
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'categoria_id' => 'required|exists:categorias,id',
            'grupo_muscular' => 'nullable|string|max:255',
            'dificultad' => 'required|in:Principiante,Intermedio,Avanzado',
            'duracion_minutos' => 'nullable|integer|min:1',
            'calorias_estimadas' => 'nullable|integer|min:0',
            'calificacion' => 'nullable|numeric|min:0|max:5',
            'equipo' => 'required|string|max:255',
            'instrucciones' => 'nullable|string',
            'imagen_url' => 'nullable|url|max:500',
            'video_url' => 'nullable|url|max:500',
            'activo' => 'nullable|boolean',
            'planes' => 'nullable|array',
            'planes.*' => 'exists:planes,id',
        ]);

        DB::beginTransaction();
        try {
            // Insertar ejercicio
            $ejercicioId = DB::table('ejercicios')->insertGetId([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'grupo_muscular' => $request->grupo_muscular,
                'dificultad' => $request->dificultad,
                'duracion_minutos' => $request->duracion_minutos,
                'calorias_estimadas' => $request->calorias_estimadas,
                'calificacion' => $request->calificacion ?? 0,
                'equipo' => $request->equipo,
                'instrucciones' => $request->instrucciones,
                'imagen_url' => $request->imagen_url,
                'video_url' => $request->video_url,
                'activo' => $request->has('activo') ? true : false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar planes al ejercicio
            if ($request->filled('planes')) {
                $planesData = [];
                foreach ($request->planes as $planId) {
                    $planesData[] = [
                        'plan_id' => $planId,
                        'ejercicio_id' => $ejercicioId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('plan_ejercicio')->insert($planesData);
            }

            DB::commit();
            return redirect()->route('ejercicios.index')->with('success', 'Ejercicio creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al crear el ejercicio: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un ejercicio
     */
    public function show($ejercicio)
    {
        $ejercicioData = DB::table('ejercicios')
            ->leftJoin('categorias', 'ejercicios.categoria_id', '=', 'categorias.id')
            ->where('ejercicios.id', $ejercicio)
            ->select(
                'ejercicios.*',
                'categorias.nombre as categoria_nombre',
                'categorias.color as categoria_color'
            )
            ->first();

        if (!$ejercicioData) {
            abort(404, 'Ejercicio no encontrado');
        }

        // Crear objeto categoria simulado
        if ($ejercicioData->categoria_id && $ejercicioData->categoria_nombre) {
            $ejercicioData->categoria = (object) [
                'id' => $ejercicioData->categoria_id,
                'nombre' => $ejercicioData->categoria_nombre,
                'color' => $ejercicioData->categoria_color,
            ];
        }

        // Agregar color_dificultad
        $ejercicioData->color_dificultad = match($ejercicioData->dificultad) {
            'Principiante' => 'bg-green-100 text-green-700',
            'Intermedio' => 'bg-yellow-100 text-yellow-700',
            'Avanzado' => 'bg-pink-100 text-pink-700',
            default => 'bg-gray-100 text-gray-700',
        };

        return view('ejercicios.show', ['ejercicio' => $ejercicioData]);
    }

    /**
     * Muestra el formulario para editar un ejercicio
     */
    public function edit($ejercicio)
    {
        $ejercicioData = DB::table('ejercicios')
            ->where('id', $ejercicio)
            ->first();

        if (!$ejercicioData) {
            abort(404, 'Ejercicio no encontrado');
        }

        $categorias = DB::table('categorias')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $planes = DB::table('planes')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $planesAsignados = DB::table('plan_ejercicio')
            ->where('ejercicio_id', $ejercicio)
            ->pluck('plan_id')
            ->toArray();

        return view('ejercicios.edit', [
            'ejercicio' => $ejercicioData,
            'categorias' => $categorias,
            'planes' => $planes,
            'planesAsignados' => $planesAsignados
        ]);
    }

    /**
     * Actualiza un ejercicio
     */
    public function update(Request $request, $ejercicio)
    {
        // Verificar que el ejercicio existe
        $ejercicioData = DB::table('ejercicios')->where('id', $ejercicio)->first();
        if (!$ejercicioData) {
            abort(404, 'Ejercicio no encontrado');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'categoria_id' => 'required|exists:categorias,id',
            'grupo_muscular' => 'nullable|string|max:255',
            'dificultad' => 'required|in:Principiante,Intermedio,Avanzado',
            'duracion_minutos' => 'nullable|integer|min:1',
            'calorias_estimadas' => 'nullable|integer|min:0',
            'calificacion' => 'nullable|numeric|min:0|max:5',
            'equipo' => 'required|string|max:255',
            'instrucciones' => 'nullable|string',
            'imagen_url' => 'nullable|url|max:500',
            'video_url' => 'nullable|url|max:500',
            'activo' => 'nullable|boolean',
            'planes' => 'nullable|array',
            'planes.*' => 'exists:planes,id',
        ]);

        DB::beginTransaction();
        try {
            DB::table('ejercicios')->where('id', $ejercicio)->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'grupo_muscular' => $request->grupo_muscular,
                'dificultad' => $request->dificultad,
                'duracion_minutos' => $request->duracion_minutos,
                'calorias_estimadas' => $request->calorias_estimadas,
                'calificacion' => $request->calificacion ?? 0,
                'equipo' => $request->equipo,
                'instrucciones' => $request->instrucciones,
                'imagen_url' => $request->imagen_url,
                'video_url' => $request->video_url,
                'activo' => $request->has('activo') ? true : false,
                'updated_at' => now(),
            ]);

            // Actualizar asignación de planes
            DB::table('plan_ejercicio')->where('ejercicio_id', $ejercicio)->delete();
            if ($request->filled('planes')) {
                $planesData = [];
                foreach ($request->planes as $planId) {
                    $planesData[] = [
                        'plan_id' => $planId,
                        'ejercicio_id' => $ejercicio,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('plan_ejercicio')->insert($planesData);
            }

            DB::commit();
            return redirect()->route('ejercicios.index')->with('success', 'Ejercicio actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el ejercicio: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un ejercicio
     */
    public function destroy($ejercicio)
    {
        // Verificar que el ejercicio existe
        $ejercicioData = DB::table('ejercicios')->where('id', $ejercicio)->first();
        if (!$ejercicioData) {
            abort(404, 'Ejercicio no encontrado');
        }

        DB::beginTransaction();
        try {
            // Eliminar relaciones
            DB::table('plan_ejercicio')->where('ejercicio_id', $ejercicio)->delete();
            DB::table('rutina_ejercicio')->where('ejercicio_id', $ejercicio)->delete();
            
            // Eliminar ejercicio
            DB::table('ejercicios')->where('id', $ejercicio)->delete();

            DB::commit();
            return redirect()->route('ejercicios.index')->with('success', 'Ejercicio eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ejercicios.index')->with('error', 'Error al eliminar el ejercicio: ' . $e->getMessage());
        }
    }
}

