<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notificacion extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notificaciones';

    protected $fillable = [
        'user_id',
        'user_data',
        'titulo',
        'mensaje',
        'tipo',
        'leida',
        'fecha_lectura',
        'datos_adicionales',
        'canal',
        'mysql_id'
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_lectura' => 'datetime',
        'user_data' => 'array',
        'datos_adicionales' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function marcarComoLeida()
    {
        $this->leida = true;
        $this->fecha_lectura = now();
        return $this->save();
    }
}