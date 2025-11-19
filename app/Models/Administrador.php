<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administradores';
    
    protected $fillable = [
        'user_id',
        'fecha_modificacion',
    ];

    protected $casts = [
        'fecha_modificacion' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

