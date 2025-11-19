<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deportista extends Model
{
    protected $table = 'deportistas';
    
    protected $fillable = [
        'user_id',
        'entrenador_id',
        'peso',
        'genero',
        'altura',
        'fecha_nacimiento',
    ];

    protected $casts = [
        'peso' => 'decimal:2',
        'altura' => 'decimal:2',
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el entrenador asignado
     */
    public function entrenador()
    {
        return $this->belongsTo(Entrenador::class, 'entrenador_id');
    }

    /**
     * Relación con progresos
     */
    public function progresos()
    {
        return $this->hasMany(Progreso::class, 'deportista_id');
    }

    /**
     * Relación con ejercicios favoritos
     */
    public function ejerciciosFavoritos()
    {
        return $this->hasMany(EjercicioFavorito::class, 'deportista_id');
    }

    /**
     * Relación con recordatorios
     */
    public function recordatorios()
    {
        return $this->hasMany(Recordatorio::class, 'deportista_id');
    }

    /**
     * Relación con gamificaciones
     */
    public function gamificaciones()
    {
        return $this->hasMany(Gamificacion::class, 'deportista_id');
    }

    /**
     * Relación con transacciones
     */
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'deportista_id');
    }

    /**
     * Relación muchos-a-muchos con ejercicios (puedeSer)
     */
    public function ejercicios()
    {
        return $this->belongsToMany(Ejercicio::class, 'deportista_ejercicio', 'deportista_id', 'ejercicio_id');
    }
}

