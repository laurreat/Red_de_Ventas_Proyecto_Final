<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HandlesDecimal128;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HandlesDecimal128;

    protected $connection = 'mongodb';
    protected $collection = 'users';

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
        'role_id',
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
        'referidos_data',
        'historial_ventas',
        'configuracion_personal',
        'avatar',
        'bio',
        'notif_email_pedidos',
        'notif_email_usuarios',
        'notif_email_sistema',
        'notif_sms_urgente',
        'notif_push_browser',
        // Configuración de Interfaz
        'tema',
        'idioma',
        'zona_horaria',
        'formato_fecha',
        'formato_hora',
        'sidebar_collapsed',
        'dashboard_widgets',
        // Configuración de Notificaciones
        'notif_email_comisiones',
        'notif_email_reportes',
        'sonido_notificaciones',
        'frecuencia_digest',
        // Configuración de Privacidad
        'perfil_publico',
        'mostrar_email',
        'mostrar_telefono',
        'mostrar_stats',
        'mostrar_ultima_conexion',
        'permitir_mensajes',
        'indexar_perfil',
        // Configuración de Seguridad
        'sesiones_multiples',
        'logout_automatico',
        'verificacion_2fa',
        'alertas_login',
        'historial_actividad',
        'refresh_automatico',
        // Configuración de Dashboard
        'widgets_activos',
        'layout_dashboard',
        'densidade_informacion',
        'mostrar_tips',
        // Favoritos y preferencias del cliente
        'favoritos',
        'productos_recientes'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
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
        'zonas_asignadas' => 'array',
        'referidos_data' => 'array',
        'historial_ventas' => 'array',
        'configuracion_personal' => 'array',
        'favoritos' => 'array',
        'productos_recientes' => 'array'
    ];

    // Relaciones embebidas - En MongoDB podemos embeber los referidos directamente
    public function getReferidosAttribute()
    {
        return $this->referidos_data ?? [];
    }

    public function setReferidosAttribute($value)
    {
        $this->attributes['referidos_data'] = $value;
    }

    // Relaciones de referencia - Para datos que cambian frecuentemente
    public function pedidosComoVendedor()
    {
        return $this->hasMany(Pedido::class, 'vendedor_id');
    }

    public function pedidosComoCliente()
    {
        return $this->hasMany(Pedido::class, 'user_id');
    }

    public function comisiones()
    {
        return $this->hasMany(Comision::class, 'user_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'user_id');
    }

    public function referidos()
    {
        return $this->hasMany(User::class, 'referido_por');
    }

    public function referidosComisiones()
    {
        return $this->hasMany(Referido::class, 'referidor_id');
    }

    public function referidor()
    {
        return $this->belongsTo(User::class, 'referido_por');
    }

    public function pedidosVendedor()
    {
        return $this->hasMany(Pedido::class, 'vendedor_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
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

    // Métodos específicos de MongoDB para manejar datos embebidos
    public function agregarReferido($referidoData)
    {
        $referidos = $this->referidos_data ?? [];
        $referidos[] = $referidoData;
        $this->referidos_data = $referidos;
        $this->total_referidos = count($referidos);
        return $this->save();
    }

    public function agregarVentaAlHistorial($ventaData)
    {
        $historial = $this->historial_ventas ?? [];
        $historial[] = array_merge($ventaData, ['fecha' => now()]);
        $this->historial_ventas = $historial;
        return $this->save();
    }

    public function actualizarConfiguracion($config)
    {
        $this->configuracion_personal = array_merge($this->configuracion_personal ?? [], $config);
        return $this->save();
    }

    // Métodos de permisos
    public function hasPermission($permission): bool
    {
        if (!$this->activo) {
            return false;
        }

        // Administradores tienen todos los permisos
        if ($this->esAdmin()) {
            return true;
        }

        // Verificar por rol personalizado
        if ($this->role_id) {
            $role = $this->role;
            if ($role && $role->active) {
                return $role->hasPermission($permission);
            }
        }

        // Verificar por rol del sistema (legacy)
        $systemRoles = Role::getSystemRoles();
        if (isset($systemRoles[$this->rol])) {
            $rolePermissions = $systemRoles[$this->rol]['permissions'];
            return in_array($permission, $rolePermissions);
        }

        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function getPermissions(): array
    {
        if ($this->esAdmin()) {
            return array_keys(Permission::getSystemPermissions());
        }

        if ($this->role_id) {
            $role = $this->role;
            if ($role && $role->active) {
                return $role->permissions ?? [];
            }
        }

        // Fallback a rol del sistema
        $systemRoles = Role::getSystemRoles();
        if (isset($systemRoles[$this->rol])) {
            return $systemRoles[$this->rol]['permissions'];
        }

        return [];
    }

    public function assignRole($roleId): bool
    {
        $this->role_id = $roleId;
        return $this->save();
    }

    public function removeRole(): bool
    {
        $this->role_id = null;
        return $this->save();
    }
}