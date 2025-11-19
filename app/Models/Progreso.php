<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progreso extends Model
{
    protected $table = 'progresos';
    
    protected $fillable = [
        'deportista_id',
        'detalle_rutina_id',
        'fecha_registro',
        'porcentaje_completado',
        'rendimiento',
        'calorias_quemadas',
        'comentarios',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'porcentaje_completado' => 'decimal:2',
        'calorias_quemadas' => 'integer',
    ];

    /**
     * Relación con el deportista
     */
    public function deportista()
    {
        return $this->belongsTo(Deportista::class, 'deportista_id');
    }

    /**
     * Relación con el detalle de rutina
     */
    public function detalleRutina()
    {
        return $this->belongsTo(DetalleRutina::class, 'detalle_rutina_id');
    }
}

