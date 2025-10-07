<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Traits\HandlesDecimal128;

class Pedido extends Model
{
    use HandlesDecimal128;

    protected $connection = 'mongodb';
    protected $collection = 'pedidos';

    protected $fillable = [
        'numero_pedido',
        'user_id',
        'cliente_data', // Datos del cliente embebidos
        'vendedor_id',
        'vendedor_data', // Datos del vendedor embebidos
        'estado',
        'total',
        'descuento',
        'total_final',
        'direccion_entrega',
        'telefono_entrega',
        'notas',
        'fecha_entrega_estimada',
        'zona_entrega_id',
        'zona_entrega_data',
        'detalles', // Array embebido de productos
        'historial_estados',
        'metodo_pago',
        'datos_entrega',
        'comisiones_calculadas',
        'stock_devuelto' // Bandera para evitar devolución duplicada
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total_final' => 'decimal:2',
        'fecha_entrega_estimada' => 'datetime',
        // 'detalles' => 'array', // Comentado: ya se maneja con getter/setter personalizado
        'historial_estados' => 'array',
        'cliente_data' => 'array',
        'vendedor_data' => 'array',
        'zona_entrega_data' => 'array',
        'datos_entrega' => 'array',
        'comisiones_calculadas' => 'array',
        'stock_devuelto' => 'boolean'
    ];

    // Relaciones de referencia para datos que cambian frecuentemente
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function comisiones()
    {
        return $this->hasMany(Comision::class, 'pedido_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    // Override para manejar tanto embebidos como relación
    public function getDetallesAttribute($value)
    {
        // Si value ya es un array, devolverlo directamente
        if (is_array($value)) {
            return $value;
        }

        // Si es una colección, devolverla
        if ($value instanceof \Illuminate\Database\Eloquent\Collection) {
            return $value;
        }

        // Para MongoDB, intentar obtener directamente del array de attributes
        if (isset($this->attributes['detalles'])) {
            $rawDetalles = $this->attributes['detalles'];
            if (is_array($rawDetalles)) {
                return $rawDetalles;
            }
        }

        // Si no hay nada, devolver array vacío
        return [];
    }

    /**
     * Obtener detalles directamente desde los atributos (sin pasar por getter)
     */
    public function getRawDetalles()
    {
        if (!isset($this->attributes) || !is_array($this->attributes)) {
            return [];
        }

        $detalles = $this->attributes['detalles'] ?? [];

        return is_array($detalles) ? $detalles : [];
    }

    /**
     * Alias para compatibilidad con vistas antiguas
     */
    public function getDetallesEmbebidosAttribute()
    {
        return $this->getRawDetalles();
    }

    public function setDetallesAttribute($detalles)
    {
        $this->attributes['detalles'] = $detalles;
    }

    public function agregarDetalle($productoData, $cantidad, $precioUnitario)
    {
        $detalles = $this->detalles;
        $subtotal = $cantidad * $precioUnitario;

        $detalle = [
            'producto_id' => $productoData['_id'],
            'producto_data' => $productoData,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal,
            'fecha_agregado' => now()
        ];

        $detalles[] = $detalle;
        $this->detalles = $detalles;

        // Recalcular totales
        $this->recalcularTotales();

        return $this;
    }

    public function recalcularTotales()
    {
        $total = 0;
        foreach ($this->detalles as $detalle) {
            $total += $detalle['subtotal'] ?? 0;
        }

        $this->total = $total;
        $this->total_final = $total - $this->descuento;
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
        return array_sum(array_column($this->detalles, 'cantidad'));
    }

    public function cambiarEstado($nuevoEstado, $motivo = null, $usuarioId = null)
    {
        $historial = $this->historial_estados ?? [];
        $historial[] = [
            'estado_anterior' => $this->estado,
            'estado_nuevo' => $nuevoEstado,
            'motivo' => $motivo,
            'fecha' => now(),
            'usuario_id' => $usuarioId ?? auth()->id()
        ];

        $this->historial_estados = $historial;
        $this->estado = $nuevoEstado;

        return $this->save();
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

    public function asignarDatosEmbebidos()
    {
        // Embeber datos del cliente
        if ($this->user_id && !$this->cliente_data) {
            $cliente = User::find($this->user_id);
            if ($cliente) {
                $this->cliente_data = [
                    '_id' => $cliente->_id,
                    'name' => $cliente->name,
                    'apellidos' => $cliente->apellidos,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono,
                    'cedula' => $cliente->cedula
                ];
            }
        }

        // Embeber datos del vendedor
        if ($this->vendedor_id && !$this->vendedor_data) {
            $vendedor = User::find($this->vendedor_id);
            if ($vendedor) {
                $this->vendedor_data = [
                    '_id' => $vendedor->_id,
                    'name' => $vendedor->name,
                    'apellidos' => $vendedor->apellidos,
                    'email' => $vendedor->email,
                    'telefono' => $vendedor->telefono
                ];
            }
        }

        return $this->save();
    }
}