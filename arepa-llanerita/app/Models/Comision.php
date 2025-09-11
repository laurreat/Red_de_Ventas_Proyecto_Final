<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comision extends Model
{
    protected $table = 'comisiones';

    protected $fillable = [
        'user_id',
        'pedido_id',
        'referido_id',
        'tipo',
        'monto_base',
        'porcentaje',
        'monto_comision',
        'estado',
        'fecha_calculo',
        'fecha_aprobacion',
        'fecha_pago',
        'descripcion',
        'detalles',
        'aprobado_por',
    ];

    protected function casts(): array
    {
        return [
            'monto_base' => 'decimal:2',
            'porcentaje' => 'decimal:2',
            'monto_comision' => 'decimal:2',
            'fecha_calculo' => 'datetime',
            'fecha_aprobacion' => 'datetime',
            'fecha_pago' => 'datetime',
            'detalles' => 'json',
        ];
    }

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function referido(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referido_id');
    }

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagada');
    }

    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('user_id', $usuarioId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeDelMes($query, $mes = null, $año = null)
    {
        $mes = $mes ?? now()->month;
        $año = $año ?? now()->year;
        
        return $query->whereYear('fecha_calculo', $año)
                    ->whereMonth('fecha_calculo', $mes);
    }

    // Métodos auxiliares
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function estaAprobada(): bool
    {
        return $this->estado === 'aprobada';
    }

    public function estaPagada(): bool
    {
        return $this->estado === 'pagada';
    }

    public function estaCancelada(): bool
    {
        return $this->estado === 'cancelada';
    }

    public function aprobar(int $aprobadoPorId): void
    {
        $this->update([
            'estado' => 'aprobada',
            'fecha_aprobacion' => now(),
            'aprobado_por' => $aprobadoPorId,
        ]);
    }

    public function marcarComoPagada(): void
    {
        $this->update([
            'estado' => 'pagada',
            'fecha_pago' => now(),
        ]);
    }

    public function calcular(): float
    {
        return ($this->monto_base * $this->porcentaje) / 100;
    }

    public function esVentaDirecta(): bool
    {
        return $this->tipo === 'venta_directa';
    }

    public function esReferido(): bool
    {
        return in_array($this->tipo, ['referido_nuevo', 'referido_compra']);
    }

    public function esLiderazgo(): bool
    {
        return $this->tipo === 'liderazgo';
    }
}
