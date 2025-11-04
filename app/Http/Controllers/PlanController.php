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
        $planes = Plan::orderBy('created_at', 'desc')->get();
        
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

        Plan::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'duracion_dias' => $request->duracion_dias,
            'activo' => $request->has('activo') ? true : false,
        ]);

        return redirect()->route('planes.index')->with('success', 'Plan creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un plan
     */
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        
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

        $plan = Plan::findOrFail($id);
        
        DB::table('planes')->where('id', $plan->id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'duracion_dias' => $request->duracion_dias,
            'activo' => $request->has('activo') ? true : false,
            'updated_at' => now(),
        ]);

        return redirect()->route('planes.index')->with('success', 'Plan actualizado exitosamente.');
    }

    /**
     * Elimina un plan
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        
        // Verificar si hay usuarios con este plan activo
        $usuariosConPlan = DB::table('plan_usuario')
            ->where('plan_id', $plan->id)
            ->where('activo', true)
            ->count();
        
        if ($usuariosConPlan > 0) {
            return redirect()->route('planes.index')->with('error', 'No se puede eliminar el plan porque hay usuarios activos con este plan.');
        }
        
        $plan->delete();

        return redirect()->route('planes.index')->with('success', 'Plan eliminado exitosamente.');
    }
}
