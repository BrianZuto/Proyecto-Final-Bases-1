<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recordatorio extends Model
{
    protected $table = 'recordatorios';
    
    protected $fillable = [
        'deportista_id',
        'frecuencia',
        'estado',
        'fecha_asignacion',
        'hora_alerta',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_asignacion' => 'date',
        'hora_alerta' => 'datetime',
    ];

    /**
     * Relación con el deportista
     */
    public function deportista()
    {
        return $this->belongsTo(Deportista::class, 'deportista_id');
    }
}

