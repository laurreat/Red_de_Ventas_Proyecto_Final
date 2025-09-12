<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'precio',
        'stock',
        'stock_minimo',
        'activo',
        'imagen',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'stock' => 'integer',
            'stock_minimo' => 'integer',
            'activo' => 'boolean',
        ];
    }

    // Relaciones
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function detallesPedidos(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }

    // Métodos auxiliares
    public function estaDisponible(): bool
    {
        return $this->activo && $this->stock > 0;
    }

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function estaAgotado(): bool
    {
        return $this->stock <= 0;
    }

    public function precioConDescuento($porcentajeDescuento): float
    {
        return $this->precio - ($this->precio * ($porcentajeDescuento / 100));
    }

    public function imagenUrl(): ?string
    {
        if ($this->imagen) {
            return asset('storage/productos/' . $this->imagen);
        }
        
        return asset('images/producto-default.jpg');
    }

    public function estadoStock(): string
    {
        if ($this->stock == 0) {
            return 'AGOTADO';
        } elseif ($this->stock <= $this->stock_minimo) {
            return 'CRÍTICO';
        } elseif ($this->stock <= ($this->stock_minimo * 2)) {
            return 'BAJO';
        }
        
        return 'OK';
    }
}
