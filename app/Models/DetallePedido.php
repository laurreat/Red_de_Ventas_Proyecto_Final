<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Traits\HandlesDecimal128;

class DetallePedido extends Model
{
    use HandlesDecimal128;

    protected $connection = 'mongodb';
    protected $collection = 'detalle_pedidos';

    protected $fillable = [
        'pedido_id',
        'pedido_data',
        'producto_id',
        'producto_data',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descuento_aplicado',
        'descuento_porcentaje',
        'precio_original',
        'especificaciones_producto',
        'notas_especiales'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descuento_aplicado' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'precio_original' => 'decimal:2',
        'pedido_data' => 'array',
        'producto_data' => 'array',
        'especificaciones_producto' => 'array',
        'notas_especiales' => 'array'
    ];

    // Relaciones de referencia
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Scopes
    public function scopeDelPedido($query, $pedidoId)
    {
        return $query->where('pedido_id', $pedidoId);
    }

    public function scopeDelProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopeConDescuento($query)
    {
        return $query->where('descuento_aplicado', '>', 0);
    }

    // Métodos auxiliares
    public function calcularSubtotal(): float
    {
        $precioFinal = $this->precio_unitario - $this->descuento_aplicado;
        return $this->cantidad * $precioFinal;
    }

    public function aplicarDescuento($porcentaje)
    {
        $this->descuento_porcentaje = $porcentaje;
        $this->descuento_aplicado = ($this->precio_unitario * $porcentaje) / 100;
        $this->subtotal = $this->calcularSubtotal();
        return $this->save();
    }

    public function tieneDescuento(): bool
    {
        return $this->descuento_aplicado > 0;
    }

    public function porcentajeDescuentoAplicado(): float
    {
        if ($this->precio_unitario <= 0) {
            return 0;
        }
        return ($this->descuento_aplicado / $this->precio_unitario) * 100;
    }

    public function precioFinalUnitario(): float
    {
        return $this->precio_unitario - $this->descuento_aplicado;
    }

    // Métodos específicos de MongoDB
    public function embederDatosProducto()
    {
        if ($this->producto_id && !$this->producto_data) {
            $producto = Producto::find($this->producto_id);
            if ($producto) {
                $this->producto_data = [
                    '_id' => $producto->_id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'imagen' => $producto->imagen,
                    'categoria_data' => $producto->categoria_data
                ];
            }
        }
        return $this->save();
    }

    public function embederDatosPedido()
    {
        if ($this->pedido_id && !$this->pedido_data) {
            $pedido = Pedido::find($this->pedido_id);
            if ($pedido) {
                $this->pedido_data = [
                    '_id' => $pedido->_id,
                    'numero_pedido' => $pedido->numero_pedido,
                    'estado' => $pedido->estado,
                    'fecha_creacion' => $pedido->created_at
                ];
            }
        }
        return $this->save();
    }

    public function agregarNotasEspeciales($notas)
    {
        $notasActuales = $this->notas_especiales ?? [];
        if (is_string($notas)) {
            $notasActuales[] = [
                'nota' => $notas,
                'fecha' => now(),
                'usuario_id' => auth()->id()
            ];
        } elseif (is_array($notas)) {
            $notasActuales[] = array_merge($notas, [
                'fecha' => now(),
                'usuario_id' => auth()->id()
            ]);
        }

        $this->notas_especiales = $notasActuales;
        return $this->save();
    }
}