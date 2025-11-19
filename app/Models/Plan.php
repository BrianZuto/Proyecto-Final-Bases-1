<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'planes';
    
    protected $fillable = [
        'nombre',
        'categoria',
        'precio',
        'duracion',
        'caracteristica',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
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
     * Relación con transacciones
     */
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'plan_id');
    }

    /**
     * Formatea el precio en pesos colombianos
     */
    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precio, 0, ',', '.') . ' COP';
    }
}

