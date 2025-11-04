<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    protected $table = 'ejercicios';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'grupo_muscular',
        'dificultad',
        'duracion_minutos',
        'calorias_estimadas',
        'calificacion',
        'equipo',
        'instrucciones',
        'imagen_url',
        'video_url',
        'activo',
    ];

    protected $casts = [
        'duracion_minutos' => 'integer',
        'calorias_estimadas' => 'integer',
        'calificacion' => 'decimal:1',
        'activo' => 'boolean',
    ];

    /**
     * Relación con categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación con planes asignados
     */
    public function planes()
    {
        return $this->belongsToMany(Plan::class, 'plan_ejercicio', 'ejercicio_id', 'plan_id')
                    ->withTimestamps();
    }

    /**
     * Obtiene el color del badge según la dificultad
     */
    public function getColorDificultadAttribute()
    {
        return match($this->dificultad) {
            'Principiante' => 'bg-green-100 text-green-700',
            'Intermedio' => 'bg-yellow-100 text-yellow-700',
            'Avanzado' => 'bg-pink-100 text-pink-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}

