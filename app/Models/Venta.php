<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Traits\HandlesDecimal128;
use Carbon\Carbon;

class Venta extends Model
{
    use HandlesDecimal128;

    protected $connection = 'mongodb';
    protected $collection = 'ventas';

    protected $fillable = [
        'numero_venta',
        'vendedor_id',
        'vendedor_data',
        'cliente_id',
        'cliente_data',
        'productos',
        'subtotal',
        'descuento',
        'iva',
        'total_final',
        'metodo_pago',
        'estado',
        'estado_pago',
        'notas',
        'fecha_venta',
        'comision_vendedor',
        'comision_lider',
        'comision_calculada',
        'historial_estados',
        'datos_entrega',
        'comprobante_pago'
    ];

    protected $casts = [
        'productos' => 'array',
        'vendedor_data' => 'array',
        'cliente_data' => 'array',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'iva' => 'decimal:2',
        'total_final' => 'decimal:2',
        'comision_vendedor' => 'decimal:2',
        'comision_lider' => 'decimal:2',
        'fecha_venta' => 'datetime',
        'comision_calculada' => 'boolean',
        'historial_estados' => 'array',
        'datos_entrega' => 'array'
    ];

    // Relaciones
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    // Scopes
    public function scopeDelVendedor($query, $vendedorId)
    {
        return $query->where('vendedor_id', $vendedorId);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorMetodoPago($query, $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeEsteMes($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopeRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    // Métodos auxiliares
    public function totalProductos(): int
    {
        return array_sum(array_column($this->productos ?? [], 'cantidad'));
    }

    public function calcularComisiones()
    {
        $comisionVendedor = ($this->total_final * 0.15); // 15%
        $comisionLider = ($this->total_final * 0.05); // 5%

        $this->comision_vendedor = $comisionVendedor;
        $this->comision_lider = $comisionLider;
        $this->comision_calculada = true;

        return $this->save();
    }

    public function cambiarEstado($nuevoEstado, $motivo = null)
    {
        $historial = $this->historial_estados ?? [];
        $historial[] = [
            'estado_anterior' => $this->estado,
            'estado_nuevo' => $nuevoEstado,
            'motivo' => $motivo,
            'fecha' => now(),
            'usuario_id' => auth()->id()
        ];

        $this->historial_estados = $historial;
        $this->estado = $nuevoEstado;

        return $this->save();
    }

    public function generarNumeroConsecutivo(): string
    {
        $ultimoNumero = self::whereYear('created_at', now()->year)->count() + 1;

        return 'VTA-' . now()->year . '-' . str_pad($ultimoNumero, 6, '0', STR_PAD_LEFT);
    }

    public function asignarDatosEmbebidos()
    {
        // Embeber datos del vendedor
        if ($this->vendedor_id && !$this->vendedor_data) {
            $vendedor = User::find($this->vendedor_id);
            if ($vendedor) {
                $this->vendedor_data = [
                    '_id' => $vendedor->_id,
                    'name' => $vendedor->name,
                    'apellidos' => $vendedor->apellidos,
                    'email' => $vendedor->email,
                    'telefono' => $vendedor->telefono,
                    'codigo_referido' => $vendedor->codigo_referido
                ];
            }
        }

        // Embeber datos del cliente
        if ($this->cliente_id && !$this->cliente_data) {
            $cliente = User::find($this->cliente_id);
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

        return $this->save();
    }

    // Estados
    public function estaCompletada(): bool
    {
        return $this->estado === 'completada';
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function estaCancelada(): bool
    {
        return $this->estado === 'cancelada';
    }

    public function pagada(): bool
    {
        return $this->estado_pago === 'pagado';
    }

    public function pendientePago(): bool
    {
        return $this->estado_pago === 'pendiente';
    }
}
