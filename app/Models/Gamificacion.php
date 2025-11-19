<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gamificacion extends Model
{
    protected $table = 'gamificaciones';
    
    protected $fillable = [
        'deportista_id',
        'fecha_asignacion',
        'logro',
        'puntos',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'puntos' => 'integer',
    ];

    /**
     * Relación con el deportista
     */
    public function deportista()
    {
        return $this->belongsTo(Deportista::class, 'deportista_id');
    }
}

