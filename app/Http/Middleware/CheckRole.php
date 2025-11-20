<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Cargar el rol del usuario - el accessor getRolAttribute() se encarga de cargarlo automáticamente
        $rol = $user->rol;
        
        if (!$rol) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        // Expandir roles si vienen separados por comas (ej: "Administrador,Entrenador")
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
        }
        $allowedRoles = array_map('trim', $allowedRoles);
        
        // Mapear "Coach" a "Entrenador" para compatibilidad
        $allowedRoles = array_map(function($role) {
            return $role === 'Coach' ? 'Entrenador' : $role;
        }, $allowedRoles);
        
        if (!in_array($rol->nombre, $allowedRoles)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}

