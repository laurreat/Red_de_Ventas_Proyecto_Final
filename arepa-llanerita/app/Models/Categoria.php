<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    // Relaciones
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    // Métodos auxiliares
    public function tieneProductos(): bool
    {
        return $this->productos()->count() > 0;
    }

    public function totalProductos(): int
    {
        return $this->productos()->count();
    }

    public function totalProductosActivos(): int
    {
        return $this->productos()->where('activo', true)->count();
    }
}
