<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    /**
     * Almacena una nueva categoría
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255|unique:categorias,nombre',
                'descripcion' => 'nullable|string|max:500',
                'color' => 'nullable|string|max:50',
            ], [
                'nombre.required' => 'El nombre de la categoría es obligatorio.',
                'nombre.unique' => 'Ya existe una categoría con ese nombre.',
            ]);

            DB::insert("
                INSERT INTO categorias (nombre, descripcion, color, activo, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [
                $validated['nombre'],
                $validated['descripcion'] ?? null,
                $validated['color'] ?? '#3B82F6',
                1,
                now(),
                now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada exitosamente.',
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
                'message' => 'Error al crear la categoría: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene todas las categorías activas
     */
    public function index()
    {
        $categorias = DB::select("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre");
        
        return response()->json($categorias);
    }

    /**
     * Actualiza una categoría
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:50',
        ]);

        DB::update("
            UPDATE categorias SET 
                nombre = ?, descripcion = ?, color = ?, updated_at = ?
            WHERE id = ?
        ", [
            $request->nombre,
            $request->descripcion,
            $request->color ?? $categoria->color,
            now(),
            $categoria->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente.',
        ]);
    }

    /**
     * Elimina una categoría
     */
    public function destroy(Categoria $categoria)
    {
        // Verificar si hay ejercicios asociados
        $ejerciciosCount = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM ejercicios 
            WHERE categoria_id = ?
        ", [$categoria->id])->total ?? 0;
        
        if ($ejerciciosCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque tiene ejercicios asociados.',
            ], 400);
        }
        
        DB::delete("DELETE FROM categorias WHERE id = ?", [$categoria->id]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente.',
        ]);
    }
}
