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
        // Construir query base con SQL directo
        $whereConditions = [];
        $params = [];

        // Filtro por nombre
        if ($request->filled('nombre')) {
            $nombre = '%' . $request->nombre . '%';
            $whereConditions[] = "(u.name LIKE ? OR u.primer_nombre LIKE ? OR u.primer_apellido LIKE ? OR u.nombre_usuario LIKE ?)";
            $params[] = $nombre;
            $params[] = $nombre;
            $params[] = $nombre;
            $params[] = $nombre;
        }

        // Filtro por rol
        if ($request->filled('rol')) {
            $rolNombre = $request->rol;
            if ($rolNombre === 'Coach') {
                $rolNombre = 'Entrenador';
            }
            $whereConditions[] = "r.nombre = ?";
            $params[] = $rolNombre;
        }

        // Filtro por plan
        if ($request->filled('plan_id')) {
            $planId = $request->plan_id;
            $whereConditions[] = "EXISTS (
                SELECT 1 FROM plan_usuario pu
                WHERE pu.user_id = u.id
                AND pu.plan_id = ?
                AND pu.activo = 1
                AND pu.fecha_fin >= ?
            )";
            $params[] = $planId;
            $params[] = now()->toDateString();
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Obtener total para paginación
        $total = DB::selectOne("
            SELECT COUNT(*) as total
            FROM users u
            LEFT JOIN roles r ON u.rol_id = r.id
            {$whereClause}
        ", $params)->total ?? 0;

        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        // Obtener datos paginados
        $usuariosData = DB::select("
            SELECT
                u.*,
                r.nombre as rol_nombre,
                r.id as rol_id
            FROM users u
            LEFT JOIN roles r ON u.rol_id = r.id
            {$whereClause}
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ", array_merge($params, [$perPage, $offset]));

        // Transformar datos para incluir planes activos
        $usuarios = collect($usuariosData)->map(function($usuario) {
            $planActivo = DB::selectOne("
                SELECT
                    p.*,
                    pu.fecha_inicio,
                    pu.fecha_fin,
                    pu.activo as pivot_activo
                FROM plan_usuario pu
                INNER JOIN planes p ON pu.plan_id = p.id
                WHERE pu.user_id = ?
                AND pu.activo = 1
                AND pu.fecha_fin >= ?
            ", [$usuario->id, now()->toDateString()]);

            if ($planActivo) {
                $planActivo->pivot = (object) [
                    'fecha_inicio' => $planActivo->fecha_inicio,
                    'fecha_fin' => $planActivo->fecha_fin,
                    'activo' => $planActivo->pivot_activo,
                ];
            }

            $usuario->plan_activo = $planActivo;

            // Crear objeto rol simulado para compatibilidad
            if ($usuario->rol_nombre) {
                $usuario->rol = (object) [
                    'id' => $usuario->rol_id,
                    'nombre' => $usuario->rol_nombre,
                ];
            }

            return $usuario;
        });

        // Crear paginador manual
        $clientes = new \Illuminate\Pagination\LengthAwarePaginator(
            $usuarios,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Obtener planes
        $planes = DB::select("SELECT * FROM planes ORDER BY nombre");

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
            'rol' => 'required|in:Administrador,Entrenador,Coach,Deportista',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Obtener el ID del rol
        $rolNombre = $request->rol === 'Coach' ? 'Entrenador' : $request->rol;
        $rol = DB::selectOne("SELECT * FROM roles WHERE nombre = ?", [$rolNombre]);

        if (!$rol) {
            return back()->withErrors(['rol' => 'El rol seleccionado no es válido.'])->withInput();
        }

        $nombreCompleto = trim(
            $request->primer_nombre . ' ' .
            ($request->segundo_nombre ?? '') . ' ' .
            $request->primer_apellido . ' ' .
            ($request->segundo_apellido ?? '')
        );

        DB::beginTransaction();
        try {
            // Crear usuario usando SQL directo
            DB::insert("
                INSERT INTO users (
                    name, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                    nombre_usuario, email, telefonos, direccion, rol_id, password,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $nombreCompleto,
                $request->primer_nombre,
                $request->segundo_nombre,
                $request->primer_apellido,
                $request->segundo_apellido,
                $request->nombre_usuario,
                $request->email,
                $request->telefonos,
                $request->direccion,
                $rol->id,
                Hash::make($request->password),
                now(),
                now()
            ]);

            $userId = DB::getPdo()->lastInsertId();

            // Crear registro en tabla de herencia según el rol
            if ($rolNombre === 'Deportista') {
                DB::insert("INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)", [$userId, now(), now()]);
            } elseif ($rolNombre === 'Entrenador') {
                DB::insert("INSERT INTO entrenadores (user_id, created_at, updated_at) VALUES (?, ?, ?)", [$userId, now(), now()]);
            } elseif ($rolNombre === 'Administrador') {
                DB::insert("INSERT INTO administradores (user_id, created_at, updated_at) VALUES (?, ?, ?)", [$userId, now(), now()]);
            }

            DB::commit();

            return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el cliente. Por favor intenta nuevamente.'])->withInput();
        }
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
        // Cargar la relación rol
        if (!$cliente->relationLoaded('rol')) {
            $cliente->load('rol');
        }

        // Obtener planes usando SQL directo
        $planes = DB::select("SELECT * FROM planes ORDER BY nombre");
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
            'rol' => 'required|in:Administrador,Entrenador,Coach,Deportista',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Obtener el ID del rol
        $rolNombre = $request->rol === 'Coach' ? 'Entrenador' : $request->rol;
        $rol = DB::selectOne("SELECT * FROM roles WHERE nombre = ?", [$rolNombre]);

        if (!$rol) {
            return back()->withErrors(['rol' => 'El rol seleccionado no es válido.'])->withInput();
        }

        // Obtener el rol actual del usuario
        $rolActual = DB::selectOne("
            SELECT r.nombre as rol_nombre
            FROM users u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE u.id = ?
        ", [$cliente->id]);

        $rolCambio = !$rolActual || $rolActual->rol_nombre !== $rolNombre;

        $nombreCompleto = trim(
            $request->primer_nombre . ' ' .
            ($request->segundo_nombre ?? '') . ' ' .
            $request->primer_apellido . ' ' .
            ($request->segundo_apellido ?? '')
        );

        DB::beginTransaction();
        try {
            // Actualizar usuario
            if ($request->filled('password')) {
                DB::update("
                    UPDATE users SET
                        name = ?, primer_nombre = ?, segundo_nombre = ?, primer_apellido = ?,
                        segundo_apellido = ?, nombre_usuario = ?, email = ?, telefonos = ?,
                        direccion = ?, rol_id = ?, password = ?, updated_at = ?
                    WHERE id = ?
                ", [
                    $nombreCompleto,
                    $request->primer_nombre,
                    $request->segundo_nombre,
                    $request->primer_apellido,
                    $request->segundo_apellido,
                    $request->nombre_usuario,
                    $request->email,
                    $request->telefonos,
                    $request->direccion,
                    $rol->id,
                    Hash::make($request->password),
                    now(),
                    $cliente->id
                ]);
            } else {
                DB::update("
                    UPDATE users SET
                        name = ?, primer_nombre = ?, segundo_nombre = ?, primer_apellido = ?,
                        segundo_apellido = ?, nombre_usuario = ?, email = ?, telefonos = ?,
                        direccion = ?, rol_id = ?, updated_at = ?
                    WHERE id = ?
                ", [
                    $nombreCompleto,
                    $request->primer_nombre,
                    $request->segundo_nombre,
                    $request->primer_apellido,
                    $request->segundo_apellido,
                    $request->nombre_usuario,
                    $request->email,
                    $request->telefonos,
                    $request->direccion,
                    $rol->id,
                    now(),
                    $cliente->id
                ]);
            }

            // Si cambió el rol, actualizar tablas de herencia
            if ($rolCambio) {
                // Eliminar de todas las tablas de herencia
                DB::delete("DELETE FROM deportistas WHERE user_id = ?", [$cliente->id]);
                DB::delete("DELETE FROM entrenadores WHERE user_id = ?", [$cliente->id]);
                DB::delete("DELETE FROM administradores WHERE user_id = ?", [$cliente->id]);

                // Crear en la tabla correspondiente al nuevo rol
                if ($rolNombre === 'Deportista') {
                    DB::insert("INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)", [$cliente->id, now(), now()]);
                } elseif ($rolNombre === 'Entrenador') {
                    DB::insert("INSERT INTO entrenadores (user_id, created_at, updated_at) VALUES (?, ?, ?)", [$cliente->id, now(), now()]);
                } elseif ($rolNombre === 'Administrador') {
                    DB::insert("INSERT INTO administradores (user_id, created_at, updated_at) VALUES (?, ?, ?)", [$cliente->id, now(), now()]);
                }

                // Si cambió de Deportista a otro rol, desactivar planes
                if ($rolActual && $rolActual->rol_nombre === 'Deportista' && $rolNombre !== 'Deportista') {
                    DB::update("UPDATE plan_usuario SET activo = 0 WHERE user_id = ? AND activo = 1", [$cliente->id]);
                }
            }

            // Asignar plan solo si el usuario es Deportista
            if ($request->filled('plan_id') && $rolNombre === 'Deportista') {
                $plan = DB::selectOne("SELECT * FROM planes WHERE id = ?", [$request->plan_id]);

                if ($plan) {
                    // Desactivar planes anteriores del usuario
                    DB::update("UPDATE plan_usuario SET activo = 0 WHERE user_id = ? AND activo = 1", [$cliente->id]);

                    // Calcular fechas
                    $fechaInicio = now()->toDateString();
                    // Extraer número de días del campo duracion (formato: "30 días" o similar)
                    $duracionDias = 30; // Valor por defecto
                    if (isset($plan->duracion)) {
                        // Extraer solo los números del campo duracion
                        preg_match('/\d+/', $plan->duracion, $matches);
                        if (!empty($matches)) {
                            $duracionDias = (int)$matches[0];
                        }
                    } elseif (isset($plan->duracion_dias)) {
                        // Fallback por si existe duracion_dias
                        $duracionDias = is_numeric($plan->duracion_dias) ? (int)$plan->duracion_dias : 30;
                    }
                    $fechaFin = now()->addDays($duracionDias)->toDateString();

                    // Crear nueva asignación de plan
                    DB::insert("
                        INSERT INTO plan_usuario (user_id, plan_id, fecha_inicio, fecha_fin, activo, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ", [$cliente->id, $plan->id, $fechaInicio, $fechaFin, 1, now(), now()]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el cliente. Por favor intenta nuevamente.'])->withInput();
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
        $tienePlanActivo = DB::selectOne("
            SELECT COUNT(*) as total
            FROM plan_usuario
            WHERE user_id = ? AND activo = 1 AND fecha_fin >= ?
        ", [$clienteId, now()->toDateString()])->total ?? 0;

        if ($tienePlanActivo > 0) {
            return redirect()->route('clientes.index')->with('error', 'No se puede eliminar el cliente porque tiene un plan activo.');
        }

        // Eliminar relaciones primero
        DB::delete("DELETE FROM plan_usuario WHERE user_id = ?", [$clienteId]);
        DB::delete("DELETE FROM rutina_usuario_progreso WHERE user_id = ?", [$clienteId]);

        // Eliminar cliente
        DB::delete("DELETE FROM users WHERE id = ?", [$clienteId]);

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Asigna un plan a un deportista
     */
    public function assignPlan(Request $request, $userId)
    {
        // Solo administradores pueden asignar planes
        $user = Auth::user();
        $rol = $user->rol;
        if (!$rol || $rol->nombre !== 'Administrador') {
            return back()->withErrors(['error' => 'No tienes permisos para realizar esta acción.']);
        }

        // Si plan_id está vacío, no hacer nada (solo se seleccionó la opción placeholder)
        if (empty($request->plan_id) || $request->plan_id === '') {
            return back();
        }

        // Si plan_id es 0, quitar el plan
        if ($request->plan_id == '0') {
            DB::update("UPDATE plan_usuario SET activo = 0 WHERE user_id = ? AND activo = 1", [$userId]);

            return redirect()->route('clientes.index')->with('success', 'Plan removido exitosamente.');
        }

        $request->validate([
            'plan_id' => 'required|exists:planes,id',
        ]);

        // Verificar que el usuario es deportista
        $usuario = DB::selectOne("
            SELECT u.id, r.nombre as rol_nombre
            FROM users u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE u.id = ?
        ", [$userId]);

        if (!$usuario) {
            return back()->withErrors(['error' => 'Usuario no encontrado.']);
        }

        if ($usuario->rol_nombre !== 'Deportista') {
            return back()->withErrors(['error' => 'Solo se pueden asignar planes a deportistas.']);
        }

        $plan = DB::selectOne("SELECT * FROM planes WHERE id = ?", [$request->plan_id]);

        if (!$plan) {
            return back()->withErrors(['error' => 'Plan no encontrado.']);
        }

        DB::beginTransaction();
        try {
            // Desactivar planes anteriores del usuario
            DB::update("UPDATE plan_usuario SET activo = 0 WHERE user_id = ? AND activo = 1", [$userId]);

            // Calcular fechas
            $fechaInicio = now()->toDateString();
            // Extraer número de días del campo duracion (formato: "30 días" o similar)
            $duracionDias = 30; // Valor por defecto
            if (isset($plan->duracion)) {
                // Extraer solo los números del campo duracion
                preg_match('/\d+/', $plan->duracion, $matches);
                if (!empty($matches)) {
                    $duracionDias = (int)$matches[0];
                }
            } elseif (isset($plan->duracion_dias)) {
                // Fallback por si existe duracion_dias
                $duracionDias = is_numeric($plan->duracion_dias) ? (int)$plan->duracion_dias : 30;
            }
            $fechaFin = now()->addDays($duracionDias)->toDateString();

            // Crear nueva asignación de plan
            DB::insert("
                INSERT INTO plan_usuario (user_id, plan_id, fecha_inicio, fecha_fin, activo, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ", [$userId, $plan->id, $fechaInicio, $fechaFin, 1, now(), now()]);

            DB::commit();

            return redirect()->route('clientes.index')->with('success', 'Plan asignado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al asignar el plan. Por favor intenta nuevamente.']);
        }
    }
}
