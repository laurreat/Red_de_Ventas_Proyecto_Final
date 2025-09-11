<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZonaEntrega extends Model
{
    protected $table = 'zonas_entrega';

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo_envio',
        'tiempo_entrega_min',
        'tiempo_entrega_max',
        'pedido_minimo',
        'barrios',
        'coordenadas',
        'referencias',
        'horarios',
        'activo',
        'vendedores_asignados',
    ];

    protected function casts(): array
    {
        return [
            'costo_envio' => 'decimal:2',
            'tiempo_entrega_min' => 'integer',
            'tiempo_entrega_max' => 'integer',
            'pedido_minimo' => 'decimal:2',
            'activo' => 'boolean',
            'barrios' => 'json',
            'coordenadas' => 'json',
            'horarios' => 'json',
            'vendedores_asignados' => 'json',
        ];
    }

    // Relaciones
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    // Métodos auxiliares
    public function tiempoEntregaPromedio(): int
    {
        return ($this->tiempo_entrega_min + $this->tiempo_entrega_max) / 2;
    }

    public function incluyeBarrio(string $barrio): bool
    {
        if (!$this->barrios) return false;
        
        return in_array(strtolower($barrio), array_map('strtolower', $this->barrios));
    }

    public function vendedorAsignado(int $vendedorId): bool
    {
        if (!$this->vendedores_asignados) return false;
        
        return in_array($vendedorId, $this->vendedores_asignados);
    }

    public function puedeRecibirPedido(float $montoTotal): bool
    {
        return $this->activo && $montoTotal >= $this->pedido_minimo;
    }
}
