<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rutina extends Model
{
    protected $table = 'rutinas';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_rutina_id',
        'nivel',
        'tiempo_estimado_minutos',
        'calorias_estimadas',
        'imagen_url',
        'activo',
    ];

    protected $casts = [
        'tiempo_estimado_minutos' => 'integer',
        'calorias_estimadas' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Relación con tipo de rutina
     */
    public function tipoRutina()
    {
        return $this->belongsTo(TipoRutina::class, 'tipo_rutina_id');
    }

    /**
     * Relación con ejercicios
     */
    public function ejercicios()
    {
        return $this->belongsToMany(Ejercicio::class, 'rutina_ejercicio', 'rutina_id', 'ejercicio_id')
                    ->withPivot('orden', 'series', 'repeticiones', 'peso', 'descanso_segundos', 'notas')
                    ->orderBy('rutina_ejercicio.orden')
                    ->withTimestamps();
    }

    /**
     * Relación con planes
     */
    public function planes()
    {
        return $this->belongsToMany(Plan::class, 'rutina_plan', 'rutina_id', 'plan_id')
                    ->withTimestamps();
    }

    /**
     * Relación con progreso de usuarios
     */
    public function progresoUsuarios()
    {
        return $this->hasMany(RutinaUsuarioProgreso::class, 'rutina_id');
    }

    /**
     * Obtiene el progreso de un usuario específico
     */
    public function progresoUsuario($userId)
    {
        return $this->progresoUsuarios()->where('user_id', $userId)->first();
    }

    /**
     * Calcula y actualiza el tiempo estimado y calorías basado en los ejercicios
     */
    public function calcularMetricas()
    {
        // Recargar la relación para asegurar que los pivots estén cargados
        $this->load('ejercicios');
        $ejercicios = $this->ejercicios;
        
        $tiempoTotal = $ejercicios->sum(function($ejercicio) {
            return $ejercicio->duracion_minutos ?? 0;
        });
        $caloriasTotal = $ejercicios->sum(function($ejercicio) {
            return $ejercicio->calorias_estimadas ?? 0;
        });
        
        // Sumar tiempo de descanso (si hay series y descanso)
        $tiempoDescanso = $ejercicios->sum(function($ejercicio) {
            $series = $ejercicio->pivot->series ?? 1;
            $descanso = $ejercicio->pivot->descanso_segundos ?? 0;
            return (($series - 1) * $descanso) / 60; // Convertir segundos a minutos
        });
        
        $this->tiempo_estimado_minutos = (int) ($tiempoTotal + $tiempoDescanso);
        $this->calorias_estimadas = (int) $caloriasTotal;
        $this->save();
        
        return $this;
    }

    /**
     * Obtiene el color del badge según la dificultad
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
}

