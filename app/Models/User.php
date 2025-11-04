<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'nombre_usuario',
        'telefonos',
        'direccion',
        'rol',
    ];
    
    /**
     * Verifica si el usuario es Administrador
     */
    public function isAdministrador()
    {
        return $this->rol === 'Administrador';
    }
    
    /**
     * Verifica si el usuario es Coach
     */
    public function isCoach()
    {
        return $this->rol === 'Coach';
    }
    
    /**
     * Verifica si el usuario es Deportista
     */
    public function isDeportista()
    {
        return $this->rol === 'Deportista';
    }
    
    /**
     * Verifica si el usuario tiene permisos de administrador
     */
    public function hasAdminAccess()
    {
        return $this->isAdministrador();
    }

    /**
     * Relación con planes asignados al usuario
     */
    public function planes()
    {
        return $this->belongsToMany(Plan::class, 'plan_usuario', 'user_id', 'plan_id')
                    ->withPivot('fecha_inicio', 'fecha_fin', 'activo')
                    ->withTimestamps();
    }

    /**
     * Obtiene el plan activo del usuario usando SQL directo
     * 
     * @return object|null
     */
    public function planActivo()
    {
        $plan = \Illuminate\Support\Facades\DB::table('plan_usuario')
            ->join('planes', 'plan_usuario.plan_id', '=', 'planes.id')
            ->where('plan_usuario.user_id', $this->id)
            ->where('plan_usuario.activo', true)
            ->where('plan_usuario.fecha_fin', '>=', now()->toDateString())
            ->select(
                'planes.*',
                'plan_usuario.fecha_inicio',
                'plan_usuario.fecha_fin',
                'plan_usuario.activo as pivot_activo'
            )
            ->first();
        
        if ($plan) {
            // Crear objeto con pivot simulado
            $plan->pivot = (object) [
                'fecha_inicio' => $plan->fecha_inicio,
                'fecha_fin' => $plan->fecha_fin,
                'activo' => $plan->pivot_activo,
            ];
        }
        
        return $plan;
    }

    /**
     * Relación con progreso de rutinas
     */
    public function rutinasProgreso()
    {
        return $this->hasMany(\App\Models\RutinaUsuarioProgreso::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
