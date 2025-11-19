<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Verifica si el perfil está completo
     */
    private function isProfileComplete($user)
    {
        $requiredFields = [
            'primer_nombre',
            'primer_apellido',
            'nombre_usuario',
            'telefonos',
            'direccion',
        ];

        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Muestra la vista de perfil del usuario
     */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Cargar la relación rol si no está cargada
        if ($user && !$user->relationLoaded('rol')) {
            $user->load('rol');
        }

        // Obtener o crear deportista
        $deportista = DB::select("SELECT * FROM deportistas WHERE user_id = ? LIMIT 1", [$user->id]);
        
        if (empty($deportista)) {
            DB::insert("INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)", [
                $user->id,
                now(),
                now()
            ]);
            $deportistaId = DB::getPdo()->lastInsertId();
            $deportista = (object) ['id' => $deportistaId];
        } else {
            $deportista = $deportista[0];
        }

        // Datos reales de la BD
        $stats = [
            'sesiones_totales' => DB::selectOne("SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'", [$user->id])->total ?? 0,
            'sesiones_mes' => DB::selectOne("SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada' AND YEAR(fecha_sesion) = ? AND MONTH(fecha_sesion) = ?", [$user->id, now()->year, now()->month])->total ?? 0,
            'racha_actual' => $this->calcularRacha($user->id),
            'logros' => DB::selectOne("SELECT COUNT(*) as total FROM gamificaciones WHERE deportista_id = ?", [$deportista->id])->total ?? 0,
            'logros_percentil' => $this->calcularPercentilLogros($deportista->id),
        ];

        // Objetivos basados en datos reales
        $sesionesMes = $stats['sesiones_mes'];
        $objetivos = [
            [
                'titulo' => '16 sesiones de entrenamiento',
                'actual' => $sesionesMes,
                'meta' => 16,
                'tipo' => 'sesiones'
            ],
            // Los otros objetivos pueden ser personalizados por el usuario en el futuro
            // Por ahora mantenemos algunos objetivos de ejemplo
        ];

        // Extraer iniciales del nombre
        $primerNombre = $user->primer_nombre ?? '';
        $primerApellido = $user->primer_apellido ?? '';

        if (empty($primerNombre) && empty($primerApellido)) {
            $nombres = explode(' ', $user->name);
            $iniciales = '';
            foreach ($nombres as $nombre) {
                if (!empty($nombre)) {
                    $iniciales .= strtoupper(substr($nombre, 0, 1));
                }
            }
            if (strlen($iniciales) > 2) {
                $iniciales = substr($iniciales, 0, 2);
            }
        } else {
            $iniciales = strtoupper(substr($primerNombre, 0, 1) . substr($primerApellido, 0, 1));
        }

        $isProfileComplete = $this->isProfileComplete($user);
        $planActivo = $user->planActivo();

        return view('profile', compact('user', 'stats', 'objetivos', 'iniciales', 'isProfileComplete', 'planActivo'));
    }

    /**
     * Muestra el formulario de edición de perfil
     */
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Cargar la relación rol si no está cargada
        if ($user && !$user->relationLoaded('rol')) {
            $user->load('rol');
        }
        $isProfileComplete = $this->isProfileComplete($user);

        return view('profile.edit', compact('user', 'isProfileComplete'));
    }

    /**
     * Actualiza el perfil del usuario
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'nombre_usuario' => 'required|string|max:255|unique:users,nombre_usuario,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telefonos' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'email.required' => 'El email es obligatorio.',
            'email.unique' => 'Este email ya está en uso.',
            'telefonos.required' => 'Los teléfonos son obligatorios.',
            'direccion.required' => 'La dirección es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $data = [
            'primer_nombre' => $request->primer_nombre,
            'segundo_nombre' => $request->segundo_nombre,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'nombre_usuario' => $request->nombre_usuario,
            'email' => $request->email,
            'telefonos' => $request->telefonos,
            'direccion' => $request->direccion,
        ];

        // Actualizar el nombre completo
        $nombreCompleto = trim(
            $request->primer_nombre . ' ' .
            ($request->segundo_nombre ?? '') . ' ' .
            $request->primer_apellido . ' ' .
            ($request->segundo_apellido ?? '')
        );
        $data['name'] = $nombreCompleto;

        // Actualizar campos del usuario
        DB::update("
            UPDATE users SET 
                primer_nombre = ?, 
                segundo_nombre = ?, 
                primer_apellido = ?, 
                segundo_apellido = ?, 
                nombre_usuario = ?, 
                email = ?, 
                telefonos = ?, 
                direccion = ?, 
                name = ?,
                updated_at = ?
            WHERE id = ?
        ", [
            $data['primer_nombre'],
            $data['segundo_nombre'],
            $data['primer_apellido'],
            $data['segundo_apellido'],
            $data['nombre_usuario'],
            $data['email'],
            $data['telefonos'],
            $data['direccion'],
            $data['name'],
            now(),
            $user->id
        ]);

        // Recargar el modelo desde la base de datos
        $user = User::find($user->id);

        return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Obtiene el estado del perfil para notificaciones
     */
    public function getProfileStatus()
    {
        /** @var User $user */
        $user = Auth::user();
        $isComplete = $this->isProfileComplete($user);

        return response()->json([
            'is_complete' => $isComplete,
            'message' => $isComplete ? null : 'Debes completar tu perfil para continuar.'
        ]);
    }

    /**
     * Calcula la racha actual de días consecutivos
     */
    private function calcularRacha($userId)
    {
        $sesiones = DB::select("
            SELECT DISTINCT fecha_sesion 
            FROM sesiones 
            WHERE user_id = ? 
            AND estado = 'completada' 
            ORDER BY fecha_sesion DESC
        ", [$userId]);
        
        $sesiones = collect($sesiones)->pluck('fecha_sesion')->toArray();

        if (empty($sesiones)) {
            return 0;
        }

        $racha = 0;
        $fechaActual = now()->toDateString();
        $fechaEsperada = $fechaActual;

        foreach ($sesiones as $fecha) {
            if ($fecha == $fechaEsperada || $fecha == date('Y-m-d', strtotime($fechaEsperada . ' -1 day'))) {
                $racha++;
                $fechaEsperada = date('Y-m-d', strtotime($fecha . ' -1 day'));
            } else {
                break;
            }
        }

        return $racha;
    }

    /**
     * Calcula el percentil de logros del usuario
     */
    private function calcularPercentilLogros($deportistaId)
    {
        $logrosUsuario = DB::selectOne("SELECT COUNT(*) as total FROM gamificaciones WHERE deportista_id = ?", [$deportistaId])->total ?? 0;

        $totalDeportistas = DB::selectOne("SELECT COUNT(*) as total FROM deportistas")->total ?? 0;
        
        if ($totalDeportistas == 0) {
            return 100;
        }

        $deportistasConMenosLogros = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM (
                SELECT deportista_id, COUNT(*) as total_logros
                FROM gamificaciones
                GROUP BY deportista_id
                HAVING total_logros < ?
            ) as subquery
        ", [$logrosUsuario])->total ?? 0;

        return round(($deportistasConMenosLogros / $totalDeportistas) * 100);
    }
}
