<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    /**
     * Muestra la lista de clientes (usuarios)
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filtro por nombre
        if ($request->filled('nombre')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nombre . '%')
                  ->orWhere('primer_nombre', 'like', '%' . $request->nombre . '%')
                  ->orWhere('primer_apellido', 'like', '%' . $request->nombre . '%')
                  ->orWhere('nombre_usuario', 'like', '%' . $request->nombre . '%');
            });
        }
        
        // Filtro por rol
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }
        
        // Filtro por plan
        if ($request->filled('plan_id')) {
            $query->whereHas('planes', function($q) use ($request) {
                $q->where('planes.id', $request->plan_id)
                  ->where('plan_usuario.activo', true)
                  ->where('plan_usuario.fecha_fin', '>=', now()->toDateString());
            });
        }
        
        $clientes = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        // Cargar planes activos para cada cliente
        $clientes->getCollection()->transform(function($cliente) {
            /** @var User $cliente */
            $cliente->plan_activo = $cliente->planActivo();
            return $cliente;
        });
        
        $planes = Plan::where('activo', true)->orderBy('nombre')->get();
        
        return view('clientes.index', compact('clientes', 'planes'));
    }

    /**
     * Muestra el formulario para crear un nuevo cliente
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Almacena un nuevo cliente
     */
    public function store(Request $request)
    {
        $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'nombre_usuario' => 'required|string|max:255|unique:users,nombre_usuario',
            'email' => 'required|string|email|max:255|unique:users',
            'telefonos' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'rol' => 'required|in:Administrador,Coach,Deportista',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $nombreCompleto = trim(
            $request->primer_nombre . ' ' . 
            ($request->segundo_nombre ?? '') . ' ' . 
            $request->primer_apellido . ' ' . 
            ($request->segundo_apellido ?? '')
        );

        User::create([
            'name' => $nombreCompleto,
            'primer_nombre' => $request->primer_nombre,
            'segundo_nombre' => $request->segundo_nombre,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'nombre_usuario' => $request->nombre_usuario,
            'email' => $request->email,
            'telefonos' => $request->telefonos,
            'direccion' => $request->direccion,
            'rol' => $request->rol,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Muestra los detalles de un cliente
     */
    public function show(User $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Muestra el formulario para editar un cliente
     */
    public function edit(User $cliente)
    {
        $planes = Plan::where('activo', true)->orderBy('nombre')->get();
        $planActivo = $cliente->planActivo();
        
        return view('clientes.edit', compact('cliente', 'planes', 'planActivo'));
    }

    /**
     * Actualiza un cliente
     */
    public function update(Request $request, User $cliente)
    {
        $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'nombre_usuario' => 'required|string|max:255|unique:users,nombre_usuario,' . $cliente->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $cliente->id,
            'telefonos' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'rol' => 'required|in:Administrador,Coach,Deportista',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $nombreCompleto = trim(
            $request->primer_nombre . ' ' . 
            ($request->segundo_nombre ?? '') . ' ' . 
            $request->primer_apellido . ' ' . 
            ($request->segundo_apellido ?? '')
        );

        $data = [
            'name' => $nombreCompleto,
            'primer_nombre' => $request->primer_nombre,
            'segundo_nombre' => $request->segundo_nombre,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'nombre_usuario' => $request->nombre_usuario,
            'email' => $request->email,
            'telefonos' => $request->telefonos,
            'direccion' => $request->direccion,
            'rol' => $request->rol,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $cliente->id)->update($data);
        $cliente = User::find($cliente->id);

        // Asignar plan solo si el usuario es Deportista
        if ($request->filled('plan_id') && $request->rol === 'Deportista') {
            $plan = Plan::findOrFail($request->plan_id);
            
            // Desactivar planes anteriores del usuario
            DB::table('plan_usuario')
                ->where('user_id', $cliente->id)
                ->where('activo', true)
                ->update(['activo' => false]);
            
            // Calcular fechas
            $fechaInicio = now()->toDateString();
            $fechaFin = now()->addDays($plan->duracion_dias)->toDateString();
            
            // Crear nueva asignación de plan
            DB::table('plan_usuario')->insert([
                'user_id' => $cliente->id,
                'plan_id' => $plan->id,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($request->rol !== 'Deportista' && $request->filled('plan_id')) {
            // Si cambió el rol a algo que no es Deportista, desactivar planes
            DB::table('plan_usuario')
                ->where('user_id', $cliente->id)
                ->where('activo', true)
                ->update(['activo' => false]);
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Elimina un cliente
     */
    public function destroy(User $cliente)
    {
        // No permitir eliminarse a sí mismo
        $clienteId = $cliente->id;
        $authUserId = Auth::id();
        
        if ($clienteId === $authUserId) {
            return redirect()->route('clientes.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Verificar si el cliente tiene planes activos antes de eliminar
        $tienePlanActivo = DB::table('plan_usuario')
            ->where('user_id', $clienteId)
            ->where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->exists();

        if ($tienePlanActivo) {
            return redirect()->route('clientes.index')->with('error', 'No se puede eliminar el cliente porque tiene un plan activo.');
        }

        // Eliminar relaciones primero
        DB::table('plan_usuario')->where('user_id', $clienteId)->delete();
        DB::table('rutina_usuario_progreso')->where('user_id', $clienteId)->delete();
        
        // Eliminar cliente
        DB::table('users')->where('id', $clienteId)->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente.');
    }
}

