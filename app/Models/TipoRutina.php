<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRutina extends Model
{
    protected $table = 'tipo_rutinas';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'color',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con rutinas
     */
    public function rutinas()
    {
        return $this->hasMany(Rutina::class, 'tipo_rutina_id');
    }
}

