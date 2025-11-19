<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrenador extends Model
{
    protected $table = 'entrenadores';
    
    protected $fillable = [
        'user_id',
        'certificaciones',
        'especialidad',
        'centro_trabajo',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con deportistas asignados
     */
    public function deportistas()
    {
        return $this->hasMany(Deportista::class, 'entrenador_id');
    }

    /**
     * Relación con rutinas creadas
     */
    public function rutinas()
    {
        return $this->hasMany(Rutina::class, 'user_id');
    }

    /**
     * Relación con consejos de nutrición publicados
     */
    public function consejosNutricion()
    {
        return $this->hasMany(ConsejoNutricion::class, 'user_id');
    }
}

