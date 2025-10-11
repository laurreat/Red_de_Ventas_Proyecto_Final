<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'movimientos_inventario';

    protected $fillable = [
        'producto_id',
        'producto_data',
        'user_id',
        'user_data',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'motivo',
        'costo_unitario',
        'costo_total',
        'proveedor_id',
        'proveedor_data',
        'numero_factura',
        'numero_lote',
        'fecha_vencimiento',
        'ubicacion_almacen',
        'documento_respaldo',
        'observaciones',
        'aprobado_por',
        'datos_adicionales'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_anterior' => 'integer',
        'stock_nuevo' => 'integer',
        'costo_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'producto_data' => 'array',
        'user_data' => 'array',
        'proveedor_data' => 'array',
        'documento_respaldo' => 'array',
        'observaciones' => 'array',
        'datos_adicionales' => 'array'
    ];

    // Relaciones de referencia
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    // Scopes
    public function scopeDelProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

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

    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeUltimoMes($query)
    {
        return $query->where('created_at', '>=', now()->subMonth());
    }

    public function scopeConCosto($query)
    {
        return $query->where('costo_total', '>', 0);
    }

    public function scopeConVencimiento($query)
    {
        return $query->whereNotNull('fecha_vencimiento');
    }

    public function scopeProximosAVencer($query, $dias = 30)
    {
        return $query->where('fecha_vencimiento', '<=', now()->addDays($dias))
                    ->where('fecha_vencimiento', '>=', now());
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

    public function afectaPositivamente(): bool
    {
        return $this->esEntrada() || ($this->esAjuste() && $this->cantidad > 0);
    }

    public function afectaNegativamente(): bool
    {
        return $this->esSalida() || ($this->esAjuste() && $this->cantidad < 0);
    }

    public function diferenciaStock(): int
    {
        return $this->stock_nuevo - $this->stock_anterior;
    }

    public function tieneVencimiento(): bool
    {
        return !is_null($this->fecha_vencimiento);
    }

    public function estaProximoAVencer($dias = 30): bool
    {
        if (!$this->tieneVencimiento()) {
            return false;
        }
        return $this->fecha_vencimiento <= now()->addDays($dias);
    }

    public function estaVencido(): bool
    {
        if (!$this->tieneVencimiento()) {
            return false;
        }
        return $this->fecha_vencimiento < now();
    }

    public function diasParaVencimiento(): ?int
    {
        if (!$this->tieneVencimiento()) {
            return null;
        }
        return $this->fecha_vencimiento->diffInDays(now(), false);
    }

    public function valorTotalMovimiento(): float
    {
        return $this->costo_total ?? ($this->cantidad * $this->costo_unitario);
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
                    'categoria_data' => $producto->categoria_data,
                    'precio' => $producto->precio,
                    'stock_minimo' => $producto->stock_minimo
                ];
            }
        }
        return $this->save();
    }

    public function embederDatosUsuario()
    {
        if ($this->user_id && !$this->user_data) {
            $usuario = User::find($this->user_id);
            if ($usuario) {
                $this->user_data = [
                    '_id' => $usuario->_id,
                    'name' => $usuario->name,
                    'apellidos' => $usuario->apellidos,
                    'email' => $usuario->email,
                    'rol' => $usuario->rol
                ];
            }
        }
        return $this->save();
    }

    public function agregarDocumentoRespaldo($documento)
    {
        $documentos = $this->documento_respaldo ?? [];
        $documentos[] = array_merge($documento, [
            'fecha_subida' => now(),
            'subido_por' => auth()->id(),
            'id_documento' => uniqid()
        ]);
        $this->documento_respaldo = $documentos;
        return $this->save();
    }

    public function agregarObservacion($observacion, $usuarioId = null)
    {
        $observaciones = $this->observaciones ?? [];
        $observaciones[] = [
            'observacion' => $observacion,
            'fecha' => now(),
            'usuario_id' => $usuarioId ?? auth()->id(),
            'id_observacion' => uniqid()
        ];
        $this->observaciones = $observaciones;
        return $this->save();
    }

    public function calcularCostoTotal()
    {
        if ($this->costo_unitario && $this->cantidad) {
            $this->costo_total = abs($this->cantidad) * $this->costo_unitario;
            return $this->save();
        }
        return false;
    }

    public function aprobar($usuarioAprobadorId, $observaciones = null)
    {
        $this->aprobado_por = $usuarioAprobadorId;

        $datosAdicionales = $this->datos_adicionales ?? [];
        $datosAdicionales['aprobacion'] = [
            'fecha_aprobacion' => now(),
            'aprobado_por' => $usuarioAprobadorId,
            'observaciones_aprobacion' => $observaciones
        ];
        $this->datos_adicionales = $datosAdicionales;

        if ($observaciones) {
            $this->agregarObservacion("Movimiento aprobado: " . $observaciones, $usuarioAprobadorId);
        }

        return $this->save();
    }

    public function cancelar($motivo, $usuarioId = null)
    {
        $datosAdicionales = $this->datos_adicionales ?? [];
        $datosAdicionales['cancelacion'] = [
            'fecha_cancelacion' => now(),
            'cancelado_por' => $usuarioId ?? auth()->id(),
            'motivo_cancelacion' => $motivo
        ];
        $this->datos_adicionales = $datosAdicionales;

        $this->agregarObservacion("Movimiento cancelado: " . $motivo, $usuarioId);

        return $this->save();
    }

    public function estaAprobado(): bool
    {
        return !is_null($this->aprobado_por);
    }

    public function estaCancelado(): bool
    {
        return isset($this->datos_adicionales['cancelacion']);
    }
}