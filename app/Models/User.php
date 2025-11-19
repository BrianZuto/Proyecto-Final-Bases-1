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
        'rol_id',
    ];
    
    /**
     * Relación con el rol
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Relación con deportista (si existe)
     */
    public function deportista()
    {
        return $this->hasOne(Deportista::class, 'user_id');
    }

    /**
     * Relación con entrenador (si existe)
     */
    public function entrenador()
    {
        return $this->hasOne(Entrenador::class, 'user_id');
    }

    /**
     * Relación con administrador (si existe)
     */
    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'user_id');
    }
    
    /**
     * Obtiene el rol del usuario como objeto, cargándolo si es necesario
     */
    private function getRolObject()
    {
        // Si la relación no está cargada, cargarla
        if (!$this->relationLoaded('rol')) {
            $this->load('rol');
        }
        
        // Obtener la relación cargada
        $rol = $this->getRelation('rol');
        
        // Si no hay relación cargada pero tenemos rol_id, cargarla directamente
        if (!$rol && $this->rol_id) {
            $rol = $this->rol()->first();
            if ($rol) {
                $this->setRelation('rol', $rol);
            }
        }
        
        return $rol;
    }
    
    /**
     * Verifica si el usuario es Administrador
     */
    public function isAdministrador()
    {
        $rol = $this->getRolObject();
        if (!$rol) {
            return false;
        }
        return $rol->nombre === 'Administrador';
    }
    
    /**
     * Verifica si el usuario es Entrenador
     */
    public function isEntrenador()
    {
        $rol = $this->getRolObject();
        if (!$rol) {
            return false;
        }
        return $rol->nombre === 'Entrenador';
    }
    
    /**
     * Verifica si el usuario es Coach (alias para Entrenador)
     */
    public function isCoach()
    {
        return $this->isEntrenador();
    }
    
    /**
     * Verifica si el usuario es Deportista
     */
    public function isDeportista()
    {
        $rol = $this->getRolObject();
        if (!$rol) {
            return false;
        }
        return $rol->nombre === 'Deportista';
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
    
    /**
     * Accessor para el rol - siempre retorna la relación, no el atributo directo
     */
    public function getRolAttribute()
    {
        // Si la relación ya está cargada, retornarla
        if ($this->relationLoaded('rol')) {
            return $this->getRelation('rol');
        }
        
        // Si tenemos rol_id, cargar y retornar la relación
        if ($this->rol_id) {
            $rol = $this->rol()->first();
            if ($rol) {
                $this->setRelation('rol', $rol);
                return $rol;
            }
        }
        
        return null;
    }
}
