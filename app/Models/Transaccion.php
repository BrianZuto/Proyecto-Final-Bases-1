<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = 'transacciones';
    
    protected $fillable = [
        'deportista_id',
        'plan_id',
        'monto',
        'fecha_pago',
        'estado',
        'metodo_pago',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    /**
     * Relación con el deportista
     */
    public function deportista()
    {
        return $this->belongsTo(Deportista::class, 'deportista_id');
    }

    /**
     * Relación con el plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}

