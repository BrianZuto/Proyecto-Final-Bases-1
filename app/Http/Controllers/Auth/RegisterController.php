<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $user = User::create([
            'name' => $request->name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'Deportista', // Rol por defecto
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}

