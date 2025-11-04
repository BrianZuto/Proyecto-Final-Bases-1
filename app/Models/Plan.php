<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'planes';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion_dias',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion_dias' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Relación con usuarios que tienen este plan
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'plan_usuario', 'plan_id', 'user_id')
                    ->withPivot('fecha_inicio', 'fecha_fin', 'activo')
                    ->withTimestamps();
    }

    /**
     * Relación con ejercicios asignados al plan
     */
    public function ejercicios()
    {
        return $this->belongsToMany(Ejercicio::class, 'plan_ejercicio', 'plan_id', 'ejercicio_id')
                    ->withTimestamps();
    }

    /**
     * Relación con rutinas asignadas al plan
     */
    public function rutinas()
    {
        return $this->belongsToMany(\App\Models\Rutina::class, 'rutina_plan', 'plan_id', 'rutina_id')
                    ->withTimestamps();
    }

    /**
     * Formatea el precio en pesos colombianos
     */
    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precio, 0, ',', '.') . ' COP';
    }
}

