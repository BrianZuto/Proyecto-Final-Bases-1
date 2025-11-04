<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoRutinaController extends Controller
{
    /**
     * Almacena un nuevo tipo de rutina
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255|unique:tipo_rutinas,nombre',
                'descripcion' => 'nullable|string|max:500',
                'color' => 'nullable|string|max:50',
            ], [
                'nombre.required' => 'El nombre del tipo de rutina es obligatorio.',
                'nombre.unique' => 'Ya existe un tipo de rutina con ese nombre.',
            ]);

            // Verificar si ya existe un tipo con ese nombre
            $existe = DB::table('tipo_rutinas')
                ->where('nombre', $validated['nombre'])
                ->exists();

            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un tipo de rutina con ese nombre.',
                ], 422);
            }

            // Insertar nuevo tipo de rutina
            $id = DB::table('tipo_rutinas')->insertGetId([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'color' => $validated['color'] ?? '#3B82F6',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de rutina creado exitosamente.',
                'id' => $id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de rutina: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene todos los tipos de rutinas activas
     */
    public function index()
    {
        $tipos = DB::table('tipo_rutinas')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return response()->json($tipos);
    }

    /**
     * Actualiza un tipo de rutina
     */
    public function update(Request $request, $tipoRutina)
    {
        try {
            // Obtener el tipo de rutina
            $tipoRutinaData = DB::table('tipo_rutinas')
                ->where('id', $tipoRutina)
                ->first();

            if (!$tipoRutinaData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de rutina no encontrado.',
                ], 404);
            }

            $validated = $request->validate([
                'nombre' => 'required|string|max:255|unique:tipo_rutinas,nombre,' . $tipoRutina,
                'descripcion' => 'nullable|string|max:500',
                'color' => 'nullable|string|max:50',
            ]);

            // Verificar si el nombre ya existe en otro registro
            $existe = DB::table('tipo_rutinas')
                ->where('nombre', $validated['nombre'])
                ->where('id', '!=', $tipoRutina)
                ->exists();

            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un tipo de rutina con ese nombre.',
                ], 422);
            }

            // Actualizar tipo de rutina
            DB::table('tipo_rutinas')
                ->where('id', $tipoRutina)
                ->update([
                    'nombre' => $validated['nombre'],
                    'descripcion' => $validated['descripcion'] ?? null,
                    'color' => $validated['color'] ?? $tipoRutinaData->color,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de rutina actualizado exitosamente.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de rutina: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina un tipo de rutina
     */
    public function destroy($tipoRutina)
    {
        // Verificar si existe el tipo de rutina
        $tipoRutinaData = DB::table('tipo_rutinas')
            ->where('id', $tipoRutina)
            ->first();

        if (!$tipoRutinaData) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de rutina no encontrado.',
            ], 404);
        }

        // Verificar si hay rutinas asociadas
        $rutinasCount = DB::table('rutinas')
            ->where('tipo_rutina_id', $tipoRutina)
            ->count();

        if ($rutinasCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el tipo de rutina porque tiene rutinas asociadas.',
            ], 400);
        }

        // Eliminar tipo de rutina
        DB::table('tipo_rutinas')
            ->where('id', $tipoRutina)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de rutina eliminado exitosamente.',
        ]);
    }
}

