<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'apellidos',
        'cedula',
        'email',
        'password',
        'telefono',
        'direccion',
        'ciudad',
        'departamento',
        'fecha_nacimiento',
        'rol',
        'activo',
        'ultimo_acceso',
        'referido_por',
        'codigo_referido',
        'total_referidos',
        'comisiones_ganadas',
        'comisiones_disponibles',
        'meta_mensual',
        'ventas_mes_actual',
        'nivel_vendedor',
        'zonas_asignadas',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
            'ultimo_acceso' => 'datetime',
            'activo' => 'boolean',
            'total_referidos' => 'integer',
            'comisiones_ganadas' => 'decimal:2',
            'comisiones_disponibles' => 'decimal:2',
            'meta_mensual' => 'decimal:2',
            'ventas_mes_actual' => 'decimal:2',
            'nivel_vendedor' => 'integer',
            'zonas_asignadas' => 'json',
        ];
    }

    // Relaciones
    public function referidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referido_por');
    }

    public function referidos(): HasMany
    {
        return $this->hasMany(User::class, 'referido_por');
    }

    public function pedidosComoVendedor(): HasMany
    {
        return $this->hasMany(Pedido::class, 'vendedor_id');
    }

    public function pedidosComoCliente(): HasMany
    {
        return $this->hasMany(Pedido::class, 'user_id');
    }

    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class);
    }

    public function referidosGestionados(): HasMany
    {
        return $this->hasMany(Referido::class, 'referidor_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }

    public function scopeVendedores($query)
    {
        return $query->whereIn('rol', ['vendedor', 'lider']);
    }

    public function scopeClientes($query)
    {
        return $query->where('rol', 'cliente');
    }

    // Métodos auxiliares
    public function nombreCompleto(): string
    {
        return $this->name . ' ' . $this->apellidos;
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esLider(): bool
    {
        return $this->rol === 'lider';
    }

    public function esVendedor(): bool
    {
        return $this->rol === 'vendedor';
    }

    public function esCliente(): bool
    {
        return $this->rol === 'cliente';
    }

    public function puedeVender(): bool
    {
        return in_array($this->rol, ['vendedor', 'lider', 'administrador']);
    }

    public function tieneReferidos(): bool
    {
        return $this->total_referidos > 0;
    }

    public function comisionesDisponiblesParaCobro(): float
    {
        return (float) $this->comisiones_disponibles;
    }

    public function porcentajeMetaAlcanzada(): float
    {
        if (!$this->meta_mensual || $this->meta_mensual == 0) {
            return 0;
        }
        
        return ($this->ventas_mes_actual / $this->meta_mensual) * 100;
    }
}
