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

        // Datos quemados - luego vendrán de la BD
        $stats = [
            'sesiones_totales' => 127,
            'sesiones_mes' => 12,
            'racha_actual' => 15,
            'logros' => 24,
            'logros_percentil' => 5,
        ];

        $objetivos = [
            [
                'titulo' => '16 sesiones de entrenamiento',
                'actual' => 12,
                'meta' => 16,
                'tipo' => 'sesiones'
            ],
            [
                'titulo' => '100kg en sentadilla',
                'actual' => 85,
                'meta' => 100,
                'tipo' => 'kg'
            ],
            [
                'titulo' => 'Perder 5kg',
                'actual' => 3,
                'meta' => 5,
                'tipo' => 'kg'
            ],
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
        DB::table('users')->where('id', $user->id)->update($data);

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
}
