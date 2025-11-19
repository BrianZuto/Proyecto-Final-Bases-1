<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    /**
     * Muestra la lista de planes
     */
    public function index()
    {
        $planes = DB::select("SELECT * FROM planes ORDER BY created_at DESC");
        
        return view('planes.index', compact('planes'));
    }

    /**
     * Muestra el formulario para crear un nuevo plan
     */
    public function create()
    {
        return view('planes.create');
    }

    /**
     * Almacena un nuevo plan
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'required|string|max:500',
            'duracion_dias' => 'required|integer|min:1',
            'activo' => 'nullable|boolean',
        ]);

        DB::insert("
            INSERT INTO planes (nombre, descripcion, precio, duracion, activo, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", [
            $request->nombre,
            $request->descripcion,
            $request->precio,
            $request->duracion_dias . ' días',
            $request->has('activo') ? 1 : 0,
            now(),
            now()
        ]);

        return redirect()->route('planes.index')->with('success', 'Plan creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un plan
     */
    public function edit($id)
    {
        $plan = DB::selectOne("SELECT * FROM planes WHERE id = ?", [$id]);
        
        if (!$plan) {
            abort(404, 'Plan no encontrado');
        }
        
        return view('planes.edit', compact('plan'));
    }

    /**
     * Actualiza un plan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'required|string|max:500',
            'duracion_dias' => 'required|integer|min:1',
            'activo' => 'nullable|boolean',
        ]);

        $plan = DB::selectOne("SELECT * FROM planes WHERE id = ?", [$id]);
        if (!$plan) {
            abort(404, 'Plan no encontrado');
        }
        
        DB::update("
            UPDATE planes SET 
                nombre = ?, descripcion = ?, precio = ?, duracion = ?, 
                activo = ?, updated_at = ?
            WHERE id = ?
        ", [
            $request->nombre,
            $request->descripcion,
            $request->precio,
            $request->duracion_dias . ' días',
            $request->has('activo') ? 1 : 0,
            now(),
            $id
        ]);

        return redirect()->route('planes.index')->with('success', 'Plan actualizado exitosamente.');
    }

    /**
     * Elimina un plan
     */
    public function destroy($id)
    {
        $plan = DB::selectOne("SELECT * FROM planes WHERE id = ?", [$id]);
        if (!$plan) {
            abort(404, 'Plan no encontrado');
        }
        
        // Verificar si hay usuarios con este plan activo
        $usuariosConPlan = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM plan_usuario 
            WHERE plan_id = ? AND activo = 1
        ", [$id])->total ?? 0;
        
        if ($usuariosConPlan > 0) {
            return redirect()->route('planes.index')->with('error', 'No se puede eliminar el plan porque hay usuarios activos con este plan.');
        }
        
        DB::delete("DELETE FROM planes WHERE id = ?", [$id]);

        return redirect()->route('planes.index')->with('success', 'Plan eliminado exitosamente.');
    }
}
