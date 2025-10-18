<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Cliente extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'clientes';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'ciudad',
        'cedula',
        'vendedor_id',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con vendedor
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    // Relación con pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    // Scope para clientes activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Obtener nombre completo formateado
    public function getNombreCompletoAttribute()
    {
        return $this->nombre;
    }
}
