<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Traits\HandlesDecimal128;

class SolicitudPago extends Model
{
    use HandlesDecimal128;

    protected $connection = 'mongodb';
    protected $collection = 'solicitudes_pago';

    protected $fillable = [
        'user_id',
        'user_data',
        'monto',
        'metodo_pago',
        'datos_pago',
        'observaciones',
        'estado',
        'fecha_procesado',
        'procesado_por',
        'comprobante',
        'notas_admin',
        'mysql_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_procesado' => 'datetime',
        'user_data' => 'array',
        'comprobante' => 'array'
    ];

    /**
     * Relación con el usuario que solicita el pago
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el admin que procesó la solicitud
     */
    public function procesadoPor()
    {
        return $this->belongsTo(User::class, 'procesado_por');
    }

    /**
     * Scopes para filtrar por estado
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazado');
    }

    /**
     * Obtener el badge de estado
     */
    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'pendiente' => 'warning',
            'aprobado' => 'info',
            'pagado' => 'success',
            'rechazado' => 'danger'
        ];

        return $badges[$this->estado] ?? 'secondary';
    }

    /**
     * Obtener el nombre del método de pago formateado
     */
    public function getMetodoPagoFormateadoAttribute()
    {
        $metodos = [
            'transferencia' => 'Transferencia Bancaria',
            'nequi' => 'Nequi',
            'daviplata' => 'Daviplata',
            'efectivo' => 'Efectivo'
        ];

        return $metodos[$this->metodo_pago] ?? ucfirst($this->metodo_pago);
    }
}
