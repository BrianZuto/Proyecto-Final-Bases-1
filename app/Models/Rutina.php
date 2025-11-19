<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rutina extends Model
{
    protected $table = 'rutinas';
    
    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'objetivo',
        'nivel',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'fecha_publicacion',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_publicacion' => 'date',
    ];

    /**
     * Relación con el usuario (entrenador) que creó la rutina
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con detalles de rutina (ejercicios con sus configuraciones)
     */
    public function detalles()
    {
        return $this->hasMany(DetalleRutina::class, 'rutina_id');
    }

    /**
     * Relación con ejercicios a través de detalles
     */
    public function ejercicios()
    {
        return $this->belongsToMany(Ejercicio::class, 'detalle_rutinas', 'rutina_id', 'ejercicio_id')
                    ->using(DetalleRutina::class)
                    ->withPivot('series', 'repeticiones', 'peso', 'tiempo', 'tiempo_descanso')
                    ->withTimestamps();
    }

    /**
     * Obtiene el color del badge según el nivel
     */
    public function getColorNivelAttribute()
    {
        return match($this->nivel) {
            'Principiante' => 'bg-green-100 text-green-700',
            'Intermedio' => 'bg-yellow-100 text-yellow-700',
            'Avanzado' => 'bg-pink-100 text-pink-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Obtiene el color del badge según el estado
     */
    public function getColorEstadoAttribute()
    {
        return match($this->estado) {
            'Borrador' => 'bg-gray-100 text-gray-700',
            'Publicada' => 'bg-green-100 text-green-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}

