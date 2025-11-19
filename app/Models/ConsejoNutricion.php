<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsejoNutricion extends Model
{
    protected $table = 'consejos_nutricion';
    
    protected $fillable = [
        'user_id',
        'titulo',
        'contenido',
    ];

    /**
     * Relación con el usuario (entrenador o administrador)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

