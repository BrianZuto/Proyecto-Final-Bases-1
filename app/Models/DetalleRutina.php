<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleRutina extends Model
{
    protected $table = 'detalle_rutinas';
    
    protected $fillable = [
        'rutina_id',
        'ejercicio_id',
        'series',
        'repeticiones',
        'peso',
        'tiempo',
        'tiempo_descanso',
    ];

    protected $casts = [
        'series' => 'integer',
        'repeticiones' => 'integer',
        'peso' => 'decimal:2',
        'tiempo' => 'integer',
        'tiempo_descanso' => 'integer',
    ];

    /**
     * Relación con la rutina
     */
    public function rutina()
    {
        return $this->belongsTo(Rutina::class, 'rutina_id');
    }

    /**
     * Relación con el ejercicio
     */
    public function ejercicio()
    {
        return $this->belongsTo(Ejercicio::class, 'ejercicio_id');
    }

    /**
     * Relación con progresos
     */
    public function progresos()
    {
        return $this->hasMany(Progreso::class, 'detalle_rutina_id');
    }
}

