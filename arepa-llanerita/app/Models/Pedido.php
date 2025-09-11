<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $fillable = [
        'numero_pedido',
        'user_id',
        'estado',
        'subtotal',
        'impuestos',
        'costo_envio',
        'total',
        'tipo_entrega',
        'direccion_entrega',
        'telefono_entrega',
        'zona_entrega_id',
        'observaciones',
        'fecha_estimada_entrega',
        'fecha_entrega_real',
        'vendedor_id',
        'fecha_asignacion',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'impuestos' => 'decimal:2',
            'costo_envio' => 'decimal:2',
            'total' => 'decimal:2',
            'fecha_estimada_entrega' => 'datetime',
            'fecha_entrega_real' => 'datetime',
            'fecha_asignacion' => 'datetime',
        ];
    }

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function zonaEntrega(): BelongsTo
    {
        return $this->belongsTo(ZonaEntrega::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // Scopes
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeConfirmados($query)
    {
        return $query->where('estado', 'confirmado');
    }

    public function scopeEntregados($query)
    {
        return $query->where('estado', 'entregado');
    }

    public function scopeDelVendedor($query, $vendedorId)
    {
        return $query->where('vendedor_id', $vendedorId);
    }

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('user_id', $clienteId);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Métodos auxiliares
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function estaConfirmado(): bool
    {
        return $this->estado === 'confirmado';
    }

    public function estaEntregado(): bool
    {
        return $this->estado === 'entregado';
    }

    public function estaCancelado(): bool
    {
        return $this->estado === 'cancelado';
    }

    public function puedeSerCancelado(): bool
    {
        return in_array($this->estado, ['pendiente', 'confirmado']);
    }

    public function totalItems(): int
    {
        return $this->detalles->sum('cantidad');
    }

    public function tiempoPreparacionTotal(): int
    {
        $maxTiempo = 0;
        foreach ($this->detalles as $detalle) {
            if ($detalle->producto->tiempo_preparacion > $maxTiempo) {
                $maxTiempo = $detalle->producto->tiempo_preparacion;
            }
        }
        return $maxTiempo;
    }

    public function calcularComisionVendedor(): float
    {
        $porcentajeComision = config('comisiones.venta_directa', 15);
        return ($this->total * $porcentajeComision) / 100;
    }

    public function generarNumeroConsecutivo(): string
    {
        $formato = config('pedidos.formato_numero', 'ARE-{YYYY}-{NNNN}');
        $ultimoNumero = self::whereYear('created_at', now()->year)->count() + 1;
        
        return str_replace(
            ['{YYYY}', '{NNNN}'],
            [now()->year, str_pad($ultimoNumero, 4, '0', STR_PAD_LEFT)],
            $formato
        );
    }
}
