<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referido extends Model
{
    protected $fillable = [
        'referidor_id',
        'referido_id',
        'fecha_referido',
        'estado',
        'fecha_activacion',
        'fecha_completado',
        'total_pedidos',
        'total_compras',
        'comision_generada',
        'canal_referido',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'fecha_referido' => 'datetime',
            'fecha_activacion' => 'datetime',
            'fecha_completado' => 'datetime',
            'total_pedidos' => 'integer',
            'total_compras' => 'decimal:2',
            'comision_generada' => 'decimal:2',
            'metadata' => 'json',
        ];
    }

    // Relaciones
    public function referidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referidor_id');
    }

    public function referido(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referido_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeDelReferidor($query, $referidorId)
    {
        return $query->where('referidor_id', $referidorId);
    }

    // Métodos auxiliares
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function estaCompletado(): bool
    {
        return $this->estado === 'completado';
    }

    public function activar(): void
    {
        $this->update([
            'estado' => 'activo',
            'fecha_activacion' => now(),
        ]);
    }

    public function completar(): void
    {
        $this->update([
            'estado' => 'completado',
            'fecha_completado' => now(),
        ]);
    }

    public function promedioComprasPorPedido(): float
    {
        if ($this->total_pedidos == 0) return 0;
        
        return $this->total_compras / $this->total_pedidos;
    }
}
