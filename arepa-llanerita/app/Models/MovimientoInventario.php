<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id',
        'user_id',
        'pedido_id',
        'tipo',
        'motivo',
        'cantidad_anterior',
        'cantidad_movimiento',
        'cantidad_actual',
        'costo_unitario',
        'costo_total',
        'observaciones',
        'lote',
        'fecha_vencimiento',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'cantidad_anterior' => 'integer',
            'cantidad_movimiento' => 'integer',
            'cantidad_actual' => 'integer',
            'costo_unitario' => 'decimal:2',
            'costo_total' => 'decimal:2',
            'fecha_vencimiento' => 'date',
            'metadata' => 'json',
        ];
    }

    // Relaciones
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    // Scopes
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo', 'salida');
    }

    public function scopeAjustes($query)
    {
        return $query->where('tipo', 'ajuste');
    }

    public function scopeDelProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('user_id', $usuarioId);
    }

    public function scopePorMotivo($query, $motivo)
    {
        return $query->where('motivo', $motivo);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeUltimosDias($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // Métodos auxiliares
    public function esEntrada(): bool
    {
        return $this->tipo === 'entrada';
    }

    public function esSalida(): bool
    {
        return $this->tipo === 'salida';
    }

    public function esAjuste(): bool
    {
        return $this->tipo === 'ajuste';
    }

    public function esTransferencia(): bool
    {
        return $this->tipo === 'transferencia';
    }

    public function impactoInventario(): int
    {
        return match($this->tipo) {
            'entrada' => $this->cantidad_movimiento,
            'salida' => -$this->cantidad_movimiento,
            'ajuste' => $this->cantidad_actual - $this->cantidad_anterior,
            'transferencia' => 0,
            default => 0
        };
    }

    public function valorMovimiento(): float
    {
        return $this->cantidad_movimiento * ($this->costo_unitario ?? 0);
    }

    public static function registrarVenta(int $productoId, int $usuarioId, int $pedidoId, int $cantidad, float $costoUnitario = null): self
    {
        $producto = Producto::find($productoId);
        
        return self::create([
            'producto_id' => $productoId,
            'user_id' => $usuarioId,
            'pedido_id' => $pedidoId,
            'tipo' => 'salida',
            'motivo' => 'venta',
            'cantidad_anterior' => $producto->stock + $cantidad,
            'cantidad_movimiento' => $cantidad,
            'cantidad_actual' => $producto->stock,
            'costo_unitario' => $costoUnitario ?? $producto->precio_costo,
            'costo_total' => $cantidad * ($costoUnitario ?? $producto->precio_costo ?? 0),
        ]);
    }

    public static function registrarEntrada(int $productoId, int $usuarioId, int $cantidad, float $costoUnitario, string $motivo = 'compra', array $metadata = []): self
    {
        $producto = Producto::find($productoId);
        
        return self::create([
            'producto_id' => $productoId,
            'user_id' => $usuarioId,
            'tipo' => 'entrada',
            'motivo' => $motivo,
            'cantidad_anterior' => $producto->stock - $cantidad,
            'cantidad_movimiento' => $cantidad,
            'cantidad_actual' => $producto->stock,
            'costo_unitario' => $costoUnitario,
            'costo_total' => $cantidad * $costoUnitario,
            'metadata' => $metadata,
        ]);
    }
}
