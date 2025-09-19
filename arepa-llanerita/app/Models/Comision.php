<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Comision extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'comisiones';

    protected $fillable = [
        'user_id',
        'user_data',
        'pedido_id',
        'pedido_data',
        'tipo',
        'porcentaje',
        'monto',
        'estado',
        'fecha_pago',
        'detalles_calculo',
        'metodo_pago',
        'mysql_id'
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
        'user_data' => 'array',
        'pedido_data' => 'array',
        'detalles_calculo' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

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
}