<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MensajeLider extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'mensajes_lider';

    protected $fillable = [
        'lider_id',
        'vendedor_id',
        'mensaje',
        'tipo_mensaje',
        'leido',
        'fecha_lectura',
        'metadata'
    ];

    protected $casts = [
        'leido' => 'boolean',
        'fecha_lectura' => 'datetime',
        'metadata' => 'array'
    ];

    // Relación con el líder que envía el mensaje
    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    // Relación con el vendedor que recibe el mensaje
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    // Scope para mensajes no leídos
    public function scopeNoLeidos($query)
    {
        return $query->where('leido', false);
    }

    // Scope para mensajes de un vendedor específico
    public function scopePorVendedor($query, $vendedorId)
    {
        return $query->where('vendedor_id', $vendedorId);
    }

    // Scope para mensajes de un líder específico
    public function scopePorLider($query, $liderId)
    {
        return $query->where('lider_id', $liderId);
    }

    // Marcar mensaje como leído
    public function marcarComoLeido()
    {
        $this->leido = true;
        $this->fecha_lectura = now();
        return $this->save();
    }
}
