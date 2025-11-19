<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Maneja el registro de un nuevo usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|confirmed',
            'email_confirmation' => 'required|string|email|max:255|same:email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => 'required|same:password',
            'terms' => 'required|accepted',
        ], [
            'email.confirmed' => 'Los emails no coinciden.',
            'email_confirmation.same' => 'Los emails no coinciden.',
            'email_confirmation.required' => 'Por favor confirma tu email.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password_confirmation.same' => 'Las contraseñas no coinciden.',
            'password_confirmation.required' => 'Por favor confirma tu contraseña.',
        ]);

        // Obtener el ID del rol "Deportista"
        $rolDeportista = DB::selectOne("SELECT * FROM roles WHERE nombre = 'Deportista' LIMIT 1");
        
        if (!$rolDeportista) {
            return back()->withErrors([
                'email' => 'Error al registrar el usuario. Por favor contacta al administrador.',
            ])->withInput();
        }

        // Crear el usuario usando SQL directo
        DB::beginTransaction();
        try {
            DB::insert("
                INSERT INTO users (name, email, password, rol_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [
                $request->name . ' ' . $request->last_name,
                $request->email,
                Hash::make($request->password),
                $rolDeportista->id,
                now(),
                now()
            ]);

            $userId = DB::getPdo()->lastInsertId();

            // Crear registro en la tabla deportistas
            DB::insert("
                INSERT INTO deportistas (user_id, created_at, updated_at)
                VALUES (?, ?, ?)
            ", [
                $userId,
                now(),
                now()
            ]);

            DB::commit();

            // Cargar el usuario para autenticarlo
            $user = User::find($userId);
            
            // Cargar la relación rol
            if ($user) {
                $user->load('rol');
            }

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'email' => 'Error al registrar el usuario. Por favor intenta nuevamente.',
            ])->withInput();
        }
    }
}

