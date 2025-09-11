<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'precio',
        'precio_costo',
        'precio_mayorista',
        'stock',
        'stock_minimo',
        'stock_maximo',
        'unidad_medida',
        'ingredientes',
        'alergenos',
        'informacion_nutricional',
        'tiempo_preparacion',
        'peso',
        'calorias',
        'imagenes',
        'imagen_principal',
        'disponible',
        'destacado',
        'requiere_preparacion',
        'permite_personalizacion',
        'slug',
        'tags',
        'orden_mostrar',
        'veces_vendido',
        'rating_promedio',
        'total_reviews',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'precio_costo' => 'decimal:2',
            'precio_mayorista' => 'decimal:2',
            'stock' => 'integer',
            'stock_minimo' => 'integer',
            'stock_maximo' => 'integer',
            'tiempo_preparacion' => 'integer',
            'peso' => 'decimal:2',
            'calorias' => 'integer',
            'disponible' => 'boolean',
            'destacado' => 'boolean',
            'requiere_preparacion' => 'boolean',
            'permite_personalizacion' => 'boolean',
            'orden_mostrar' => 'integer',
            'veces_vendido' => 'integer',
            'rating_promedio' => 'decimal:2',
            'total_reviews' => 'integer',
            'ingredientes' => 'json',
            'alergenos' => 'json',
            'imagenes' => 'json',
            'tags' => 'json',
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
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
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
              ->orWhere('descripcion', 'like', "%{$termino}%")
              ->orWhere('codigo', 'like', "%{$termino}%");
        });
    }

    // Métodos auxiliares
    public function estaDisponible(): bool
    {
        return $this->disponible && $this->stock > 0;
    }

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function estaAgotado(): bool
    {
        return $this->stock <= 0;
    }

    public function margenGanancia(): float
    {
        if (!$this->precio_costo || $this->precio_costo == 0) {
            return 0;
        }
        
        return (($this->precio - $this->precio_costo) / $this->precio_costo) * 100;
    }

    public function precioConDescuento($porcentajeDescuento): float
    {
        return $this->precio - ($this->precio * ($porcentajeDescuento / 100));
    }

    public function imagenPrincipalUrl(): ?string
    {
        if ($this->imagen_principal) {
            return asset('storage/productos/' . $this->imagen_principal);
        }
        
        if ($this->imagenes && is_array($this->imagenes) && count($this->imagenes) > 0) {
            return asset('storage/productos/' . $this->imagenes[0]);
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
