<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $isProfileComplete = $this->isProfileComplete($user);
                $view->with('isProfileComplete', $isProfileComplete);
            }
        });
    }
    
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
}
