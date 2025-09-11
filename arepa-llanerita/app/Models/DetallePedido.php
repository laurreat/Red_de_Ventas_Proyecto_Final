<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedidos';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observaciones_producto',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    // Relaciones
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Métodos auxiliares
    public function calcularSubtotal(): float
    {
        return $this->cantidad * $this->precio_unitario;
    }

    public function tieneObservaciones(): bool
    {
        return !empty($this->observaciones_producto);
    }

    public function margenGanancia(): float
    {
        if (!$this->producto->precio_costo || $this->producto->precio_costo == 0) {
            return 0;
        }
        
        $costoTotal = $this->cantidad * $this->producto->precio_costo;
        $ganancia = $this->subtotal - $costoTotal;
        
        return ($ganancia / $costoTotal) * 100;
    }
}
