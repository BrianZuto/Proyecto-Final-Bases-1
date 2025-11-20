<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Maneja el intento de login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Cargar la relación rol para evitar errores
            // El accessor getRolAttribute() se encarga de cargar el rol automáticamente
            $user = Auth::user();
            if ($user) {
                // Acceder al rol para activar el accessor y cargar la relación
                $user->rol;
            }

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas no son correctas.'],
        ]);
    }

    /**
     * Cierra la sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

