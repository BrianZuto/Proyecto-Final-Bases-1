<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EjercicioFavorito extends Model
{
    protected $table = 'ejercicio_favoritos';
    
    protected $fillable = [
        'deportista_id',
        'ejercicio_id',
        'fecha_agregado',
        'nivel_gusto',
    ];

    protected $casts = [
        'fecha_agregado' => 'date',
        'nivel_gusto' => 'integer',
    ];

    /**
     * Relación con el deportista
     */
    public function deportista()
    {
        return $this->belongsTo(Deportista::class, 'deportista_id');
    }

    /**
     * Relación con el ejercicio
     */
    public function ejercicio()
    {
        return $this->belongsTo(Ejercicio::class, 'ejercicio_id');
    }
}

