<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Categoria extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'imagen',
        'orden',
        'productos_count',
        'configuracion'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
        'productos_count' => 'integer',
        'configuracion' => 'array'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden', 'asc');
    }
}