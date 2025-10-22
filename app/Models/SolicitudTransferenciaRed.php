<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitudTransferenciaRed extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'solicitudes_transferencia_red';

    protected $fillable = [
        'cliente_id',
        'cliente_nombre',
        'cliente_email',
        'cliente_codigo_referido',
        'vendedor_id',
        'vendedor_nombre',
        'vendedor_email',
        'total_nodos',
        'nodos_ids',
        'estado', // 'pendiente', 'aprobada', 'rechazada', 'cancelada'
        'mensaje_cliente',
        'mensaje_vendedor',
        'fecha_solicitud',
        'fecha_respuesta',
        'aprobada_por',
        'motivo_rechazo'
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
        'total_nodos' => 'integer',
        'nodos_ids' => 'array'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobada_por');
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

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    // Métodos auxiliares
    public function esPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function esAprobada()
    {
        return $this->estado === 'aprobada';
    }

    public function esRechazada()
    {
        return $this->estado === 'rechazada';
    }

    public function aprobar($aprobadorId, $mensaje = null)
    {
        $this->estado = 'aprobada';
        $this->aprobada_por = $aprobadorId;
        $this->fecha_respuesta = now();
        if ($mensaje) {
            $this->mensaje_vendedor = $mensaje;
        }
        return $this->save();
    }

    public function rechazar($aprobadorId, $motivo)
    {
        $this->estado = 'rechazada';
        $this->aprobada_por = $aprobadorId;
        $this->fecha_respuesta = now();
        $this->motivo_rechazo = $motivo;
        return $this->save();
    }

    public function cancelar()
    {
        $this->estado = 'cancelada';
        return $this->save();
    }
}
