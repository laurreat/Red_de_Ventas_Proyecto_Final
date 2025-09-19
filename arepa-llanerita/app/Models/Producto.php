<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Producto extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'categoria_data', // Embebemos datos de la categoría
        'precio',
        'stock',
        'stock_minimo',
        'activo',
        'imagen',
        'imagenes_adicionales',
        'especificaciones',
        'tiempo_preparacion',
        'ingredientes',
        'historial_precios',
        'reviews'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean',
        'tiempo_preparacion' => 'integer',
        'imagenes_adicionales' => 'array',
        'especificaciones' => 'array',
        'ingredientes' => 'array',
        'historial_precios' => 'array',
        'reviews' => 'array',
        'categoria_data' => 'array'
    ];

    // Relación embebida con categoría
    public function getCategoriaAttribute()
    {
        return $this->categoria_data;
    }

    public function setCategoriaAttribute($categoria)
    {
        if (is_object($categoria)) {
            $this->attributes['categoria_data'] = $categoria->toArray();
            $this->attributes['categoria_id'] = $categoria->_id;
        } elseif (is_array($categoria)) {
            $this->attributes['categoria_data'] = $categoria;
            $this->attributes['categoria_id'] = $categoria['_id'] ?? null;
        }
    }

    // Relaciones de referencia
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function detallesPedidos()
    {
        return $this->hasMany(DetallePedido::class, 'producto_id');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
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
        return $query->where('stock', '<=', '$stock_minimo');
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

    // Métodos específicos para MongoDB
    public function agregarReview($reviewData)
    {
        $reviews = $this->reviews ?? [];
        $reviews[] = array_merge($reviewData, [
            'fecha' => now(),
            'id' => uniqid()
        ]);
        $this->reviews = $reviews;
        return $this->save();
    }

    public function actualizarPrecio($nuevoPrecio, $motivo = null)
    {
        $historial = $this->historial_precios ?? [];
        $historial[] = [
            'precio_anterior' => $this->precio,
            'precio_nuevo' => $nuevoPrecio,
            'motivo' => $motivo,
            'fecha' => now(),
            'usuario_id' => auth()->id()
        ];

        $this->historial_precios = $historial;
        $this->precio = $nuevoPrecio;

        return $this->save();
    }

    public function agregarImagenes($imagenes)
    {
        $imagenesActuales = $this->imagenes_adicionales ?? [];
        $this->imagenes_adicionales = array_merge($imagenesActuales, $imagenes);
        return $this->save();
    }

    public function promedioReviews()
    {
        $reviews = $this->reviews ?? [];
        if (empty($reviews)) {
            return 0;
        }

        $sum = array_sum(array_column($reviews, 'calificacion'));
        return round($sum / count($reviews), 2);
    }
}