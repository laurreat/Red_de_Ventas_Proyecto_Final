<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SolicitudRetiro extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_retiro';

    protected $fillable = [
        'user_id',
        'monto_solicitado',
        'metodo_pago',
        'datos_pago',
        'estado',
        'fecha_solicitud',
        'fecha_procesado',
        'notas_admin',
        'comprobante_pago'
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_procesado' => 'datetime',
        'monto_solicitado' => 'decimal:2'
    ];

    // Estados posibles
    const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_proceso' => 'En Proceso',
        'completado' => 'Completado',
        'rechazado' => 'Rechazado'
    ];

    // Métodos de pago
    const METODOS_PAGO = [
        'transferencia' => 'Transferencia Bancaria',
        'nequi' => 'Nequi',
        'daviplata' => 'Daviplata'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comisiones()
    {
        return $this->belongsToMany(Comision::class, 'solicitud_retiro_comisiones');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    // Accessors
    public function getEstadoFormateadoAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getMetodoPagoFormateadoAttribute()
    {
        return self::METODOS_PAGO[$this->metodo_pago] ?? $this->metodo_pago;
    }

    public function getTiempoEsperaAttribute()
    {
        if ($this->estado === 'completado' && $this->fecha_procesado) {
            return $this->fecha_solicitud->diffInDays($this->fecha_procesado);
        }

        if ($this->estado === 'pendiente' || $this->estado === 'en_proceso') {
            return $this->fecha_solicitud->diffInDays(Carbon::now());
        }

        return null;
    }

    // Mutators
    public function setDatosPagoAttribute($value)
    {
        // Encriptar datos sensibles de pago
        $this->attributes['datos_pago'] = encrypt($value);
    }

    public function getDatosPagoAttribute($value)
    {
        // Desencriptar datos de pago
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    // Métodos adicionales
    public function puedeSerCancelada()
    {
        return in_array($this->estado, ['pendiente', 'en_proceso']);
    }

    public function puedeSerProcesada()
    {
        return $this->estado === 'pendiente';
    }

    public function marcarComoProcesada($notasAdmin = null)
    {
        $this->update([
            'estado' => 'en_proceso',
            'notas_admin' => $notasAdmin
        ]);
    }

    public function marcarComoCompletada($comprobantePago = null, $notasAdmin = null)
    {
        $this->update([
            'estado' => 'completado',
            'fecha_procesado' => Carbon::now(),
            'comprobante_pago' => $comprobantePago,
            'notas_admin' => $notasAdmin
        ]);

        // Marcar comisiones como pagadas
        $this->comisiones()->update(['estado' => 'pagado']);
    }

    public function rechazar($motivo)
    {
        $this->update([
            'estado' => 'rechazado',
            'fecha_procesado' => Carbon::now(),
            'notas_admin' => $motivo
        ]);

        // Regresar comisiones a estado pendiente
        $this->comisiones()->update(['estado' => 'pendiente']);
    }
}