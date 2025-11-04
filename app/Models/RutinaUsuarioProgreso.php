<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutinaUsuarioProgreso extends Model
{
    protected $table = 'rutina_usuario_progreso';
    
    protected $fillable = [
        'user_id',
        'rutina_id',
        'porcentaje_completado',
        'fecha_inicio',
        'fecha_ultima_sesion',
        'sesiones_completadas',
    ];

    protected $casts = [
        'porcentaje_completado' => 'decimal:2',
        'sesiones_completadas' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_ultima_sesion' => 'date',
    ];

    /**
     * Relación con usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con rutina
     */
    public function rutina()
    {
        return $this->belongsTo(Rutina::class, 'rutina_id');
    }
}

