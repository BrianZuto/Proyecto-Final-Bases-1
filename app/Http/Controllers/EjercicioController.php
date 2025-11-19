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

        // Construir query base con SQL directo
        $whereConditions = ["e.activo = 1"];
        $params = [];

        if (!$user->isAdministrador()) {
            // Obtener IDs de ejercicios asignados al plan del usuario
            $ejerciciosPlanIds = DB::select("SELECT ejercicio_id FROM plan_ejercicio WHERE plan_id = ?", [$planActivo->id]);
            $ejerciciosPlanIds = collect($ejerciciosPlanIds)->pluck('ejercicio_id')->toArray();
            
            if (empty($ejerciciosPlanIds)) {
                $ejerciciosPlanIds = [0]; // Forzar resultado vacío
            }
            
            $placeholders = implode(',', array_fill(0, count($ejerciciosPlanIds), '?'));
            $whereConditions[] = "e.id IN ({$placeholders})";
            $params = array_merge($params, $ejerciciosPlanIds);
        }

        // Filtro por búsqueda
        if ($request->filled('buscar')) {
            $buscar = '%' . $request->buscar . '%';
            $whereConditions[] = "(e.nombre LIKE ? OR e.descripcion LIKE ? OR e.grupo_muscular LIKE ?)";
            $params[] = $buscar;
            $params[] = $buscar;
            $params[] = $buscar;
        }

        // Filtro por categoría
        if ($request->filled('categoria_id') && $request->categoria_id !== 'Todos') {
            $whereConditions[] = "e.categoria_id = ?";
            $params[] = $request->categoria_id;
        }

        // Filtro por dificultad
        if ($request->filled('dificultad') && $request->dificultad !== 'Todos') {
            $whereConditions[] = "e.dificultad = ?";
            $params[] = $request->dificultad;
        }

        $whereClause = implode(' AND ', $whereConditions);

        // Paginación
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        // Contar total
        $total = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM ejercicios e
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE {$whereClause}
        ", $params)->total ?? 0;

        // Obtener ejercicios
        $ejerciciosData = DB::select("
            SELECT 
                e.*,
                c.nombre as categoria_nombre,
                c.color as categoria_color
            FROM ejercicios e
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE {$whereClause}
            ORDER BY e.nombre
            LIMIT ? OFFSET ?
        ", array_merge($params, [$perPage, $offset]));

        // Transformar datos
        $ejercicios = collect($ejerciciosData)->map(function($ejercicio) {
            if ($ejercicio->categoria_id && $ejercicio->categoria_nombre) {
                $ejercicio->categoria = (object) [
                    'id' => $ejercicio->categoria_id,
                    'nombre' => $ejercicio->categoria_nombre,
                    'color' => $ejercicio->categoria_color,
                ];
            }
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
        if (!$user->isAdministrador()) {
            $categoriasIds = collect($ejerciciosData)->pluck('categoria_id')->filter()->unique()->toArray();
            if (!empty($categoriasIds)) {
                $placeholders = implode(',', array_fill(0, count($categoriasIds), '?'));
                $categorias = DB::select("
                    SELECT * FROM categorias 
                    WHERE activo = 1 AND id IN ({$placeholders})
                    ORDER BY nombre
                ", $categoriasIds);
            } else {
                $categorias = [];
            }
        } else {
            $categorias = DB::select("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre");
        }

        return view('ejercicios.index', compact('ejercicios', 'categorias', 'planActivo'));
    }

    /**
     * Muestra el formulario para crear un nuevo ejercicio
     */
    public function create()
    {
        $categorias = DB::select("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre");
        $planes = DB::select("SELECT * FROM planes WHERE activo = 1 ORDER BY nombre");

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
            DB::insert("
                INSERT INTO ejercicios (
                    nombre, descripcion, categoria_id, grupo_muscular, dificultad,
                    duracion_minutos, calorias_estimadas, calificacion, equipo,
                    instrucciones, imagen_url, video_url, activo, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $request->nombre,
                $request->descripcion,
                $request->categoria_id,
                $request->grupo_muscular,
                $request->dificultad,
                $request->duracion_minutos,
                $request->calorias_estimadas,
                $request->calificacion ?? 0,
                $request->equipo,
                $request->instrucciones,
                $request->imagen_url,
                $request->video_url,
                $request->has('activo') ? 1 : 0,
                now(),
                now()
            ]);

            $ejercicioId = DB::getPdo()->lastInsertId();

            // Asignar planes al ejercicio
            if ($request->filled('planes')) {
                foreach ($request->planes as $planId) {
                    DB::insert("
                        INSERT INTO plan_ejercicio (plan_id, ejercicio_id, created_at, updated_at)
                        VALUES (?, ?, ?, ?)
                    ", [$planId, $ejercicioId, now(), now()]);
                }
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
        $ejercicioData = DB::selectOne("
            SELECT 
                e.*,
                c.nombre as categoria_nombre,
                c.color as categoria_color
            FROM ejercicios e
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE e.id = ?
        ", [$ejercicio]);

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

        // Obtener progreso del usuario
        $user = Auth::user();
        $progresoHoy = DB::selectOne("
            SELECT * FROM ejercicio_usuario_progreso 
            WHERE user_id = ? AND ejercicio_id = ? AND fecha_ejecucion = ?
        ", [$user->id, $ejercicio, now()->toDateString()]);

        $vecesCompletado = DB::selectOne("
            SELECT SUM(veces_completado) as total 
            FROM ejercicio_usuario_progreso 
            WHERE user_id = ? AND ejercicio_id = ?
        ", [$user->id, $ejercicio])->total ?? 0;

        return view('ejercicios.show', [
            'ejercicio' => $ejercicioData,
            'progresoHoy' => $progresoHoy,
            'vecesCompletado' => $vecesCompletado
        ]);
    }

    /**
     * Marca un ejercicio individual como completado
     */
    public function complete(Request $request, $ejercicio)
    {
        $request->validate([
            'rendimiento' => 'nullable|string|max:1000',
            'calorias_quemadas' => 'nullable|integer|min:0',
            'comentarios' => 'nullable|string|max:1000',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Verificar que el ejercicio existe
        $ejercicioData = DB::selectOne("SELECT * FROM ejercicios WHERE id = ?", [$ejercicio]);
        if (!$ejercicioData) {
            return response()->json(['error' => 'Ejercicio no encontrado.'], 404);
        }

        $hoy = now()->toDateString();

        // Verificar si ya está completado hoy
        $progresoExistente = DB::selectOne("
            SELECT * FROM ejercicio_usuario_progreso 
            WHERE user_id = ? AND ejercicio_id = ? AND fecha_ejecucion = ?
        ", [$user->id, $ejercicio, $hoy]);

        if ($progresoExistente) {
            // Si ya existe, incrementar veces completado
            DB::update("
                UPDATE ejercicio_usuario_progreso SET 
                    veces_completado = veces_completado + 1,
                    rendimiento = ?,
                    calorias_quemadas = ?,
                    comentarios = ?,
                    updated_at = ?
                WHERE id = ?
            ", [
                $request->rendimiento ?? $progresoExistente->rendimiento,
                $request->calorias_quemadas ?? $progresoExistente->calorias_quemadas,
                $request->comentarios ?? $progresoExistente->comentarios,
                now(),
                $progresoExistente->id
            ]);
        } else {
            // Si no existe, crear nuevo registro
            DB::insert("
                INSERT INTO ejercicio_usuario_progreso (
                    user_id, ejercicio_id, fecha_ejecucion, veces_completado,
                    rendimiento, calorias_quemadas, comentarios, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $user->id,
                $ejercicio,
                $hoy,
                1,
                $request->rendimiento,
                $request->calorias_quemadas,
                $request->comentarios,
                now(),
                now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ejercicio marcado como completado',
        ]);
    }

    /**
     * Muestra el formulario para editar un ejercicio
     */
    public function edit($ejercicio)
    {
        $ejercicioData = DB::selectOne("SELECT * FROM ejercicios WHERE id = ?", [$ejercicio]);

        if (!$ejercicioData) {
            abort(404, 'Ejercicio no encontrado');
        }

        $categorias = DB::select("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre");
        $planes = DB::select("SELECT * FROM planes WHERE activo = 1 ORDER BY nombre");

        $planesAsignados = DB::select("SELECT plan_id FROM plan_ejercicio WHERE ejercicio_id = ?", [$ejercicio]);
        $planesAsignados = collect($planesAsignados)->pluck('plan_id')->toArray();

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
        $ejercicioData = DB::selectOne("SELECT * FROM ejercicios WHERE id = ?", [$ejercicio]);
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
            DB::update("
                UPDATE ejercicios SET 
                    nombre = ?, descripcion = ?, categoria_id = ?, grupo_muscular = ?,
                    dificultad = ?, duracion_minutos = ?, calorias_estimadas = ?,
                    calificacion = ?, equipo = ?, instrucciones = ?, imagen_url = ?,
                    video_url = ?, activo = ?, updated_at = ?
                WHERE id = ?
            ", [
                $request->nombre,
                $request->descripcion,
                $request->categoria_id,
                $request->grupo_muscular,
                $request->dificultad,
                $request->duracion_minutos,
                $request->calorias_estimadas,
                $request->calificacion ?? 0,
                $request->equipo,
                $request->instrucciones,
                $request->imagen_url,
                $request->video_url,
                $request->has('activo') ? 1 : 0,
                now(),
                $ejercicio
            ]);

            // Actualizar asignación de planes
            DB::delete("DELETE FROM plan_ejercicio WHERE ejercicio_id = ?", [$ejercicio]);
            if ($request->filled('planes')) {
                foreach ($request->planes as $planId) {
                    DB::insert("
                        INSERT INTO plan_ejercicio (plan_id, ejercicio_id, created_at, updated_at)
                        VALUES (?, ?, ?, ?)
                    ", [$planId, $ejercicio, now(), now()]);
                }
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
        $ejercicioData = DB::selectOne("SELECT * FROM ejercicios WHERE id = ?", [$ejercicio]);
        if (!$ejercicioData) {
            abort(404, 'Ejercicio no encontrado');
        }

        DB::beginTransaction();
        try {
            // Eliminar relaciones
            DB::delete("DELETE FROM plan_ejercicio WHERE ejercicio_id = ?", [$ejercicio]);
            DB::delete("DELETE FROM detalle_rutinas WHERE ejercicio_id = ?", [$ejercicio]);
            
            // Eliminar ejercicio
            DB::delete("DELETE FROM ejercicios WHERE id = ?", [$ejercicio]);

            DB::commit();
            return redirect()->route('ejercicios.index')->with('success', 'Ejercicio eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ejercicios.index')->with('error', 'Error al eliminar el ejercicio: ' . $e->getMessage());
        }
    }
}
