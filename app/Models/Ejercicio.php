<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    protected $table = 'ejercicios';
    
    protected $fillable = [
        'nombre',
        'grupo_muscular',
        'material_necesario',
    ];

    /**
     * Relación con detalles de rutinas
     */
    public function detallesRutinas()
    {
        return $this->hasMany(DetalleRutina::class, 'ejercicio_id');
    }

    /**
     * Relación con rutinas a través de detalles
     */
    public function rutinas()
    {
        return $this->belongsToMany(Rutina::class, 'detalle_rutinas', 'ejercicio_id', 'rutina_id')
                    ->using(DetalleRutina::class)
                    ->withPivot('series', 'repeticiones', 'peso', 'tiempo', 'tiempo_descanso')
                    ->withTimestamps();
    }

    /**
     * Relación con deportistas que pueden hacer este ejercicio
     */
    public function deportistas()
    {
        return $this->belongsToMany(Deportista::class, 'deportista_ejercicio', 'ejercicio_id', 'deportista_id');
    }

    /**
     * Relación con ejercicios favoritos
     */
    public function favoritos()
    {
        return $this->hasMany(EjercicioFavorito::class, 'ejercicio_id');
    }
}

