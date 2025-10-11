<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Referido extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'referidos';

    protected $fillable = [
        'referidor_id',
        'referidor_data',
        'referido_id',
        'referido_data',
        'fecha_registro',
        'activo',
        'nivel',
        'comisiones_generadas',
        'comisiones_pendientes',
        'comisiones_pagadas',
        'total_ventas_referido',
        'meta_referido',
        'estado_referido',
        'historial_comisiones',
        'codigo_promocional_usado',
        'bonificaciones_especiales'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'activo' => 'boolean',
        'nivel' => 'integer',
        'comisiones_generadas' => 'decimal:2',
        'comisiones_pendientes' => 'decimal:2',
        'comisiones_pagadas' => 'decimal:2',
        'total_ventas_referido' => 'decimal:2',
        'meta_referido' => 'decimal:2',
        'referidor_data' => 'array',
        'referido_data' => 'array',
        'historial_comisiones' => 'array',
        'bonificaciones_especiales' => 'array'
    ];

    // Relaciones de referencia
    public function referidor()
    {
        return $this->belongsTo(User::class, 'referidor_id');
    }

    public function referido()
    {
        return $this->belongsTo(User::class, 'referido_id');
    }

    public function comisiones()
    {
        return $this->hasMany(Comision::class, 'referido_relacion_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDelReferidor($query, $referidorId)
    {
        return $query->where('referidor_id', $referidorId);
    }

    public function scopeDelReferido($query, $referidoId)
    {
        return $query->where('referido_id', $referidoId);
    }

    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    public function scopeConComisionesPendientes($query)
    {
        return $query->where('comisiones_pendientes', '>', 0);
    }

    public function scopeMetaAlcanzada($query)
    {
        return $query->whereRaw('total_ventas_referido >= meta_referido');
    }

    // Métodos auxiliares
    public function esDirecto(): bool
    {
        return $this->nivel === 1;
    }

    public function esSegundoNivel(): bool
    {
        return $this->nivel === 2;
    }

    public function porcentajeMetaAlcanzada(): float
    {
        if (!$this->meta_referido || $this->meta_referido == 0) {
            return 0;
        }
        return ($this->total_ventas_referido / $this->meta_referido) * 100;
    }

    public function comisionesTotalesGeneradas(): float
    {
        return $this->comisiones_pagadas + $this->comisiones_pendientes;
    }

    public function estaActivoYProductivo(): bool
    {
        return $this->activo && $this->total_ventas_referido > 0;
    }

    public function diasDesdeRegistro(): int
    {
        return $this->fecha_registro->diffInDays(now());
    }

    // Métodos específicos de MongoDB
    public function embederDatosReferidor()
    {
        if ($this->referidor_id && !$this->referidor_data) {
            $referidor = User::find($this->referidor_id);
            if ($referidor) {
                $this->referidor_data = [
                    '_id' => $referidor->_id,
                    'name' => $referidor->name,
                    'apellidos' => $referidor->apellidos,
                    'email' => $referidor->email,
                    'telefono' => $referidor->telefono,
                    'rol' => $referidor->rol,
                    'codigo_referido' => $referidor->codigo_referido
                ];
            }
        }
        return $this->save();
    }

    public function embederDatosReferido()
    {
        if ($this->referido_id && !$this->referido_data) {
            $referido = User::find($this->referido_id);
            if ($referido) {
                $this->referido_data = [
                    '_id' => $referido->_id,
                    'name' => $referido->name,
                    'apellidos' => $referido->apellidos,
                    'email' => $referido->email,
                    'telefono' => $referido->telefono,
                    'rol' => $referido->rol,
                    'fecha_registro' => $referido->created_at
                ];
            }
        }
        return $this->save();
    }

    public function agregarComisionAlHistorial($comisionData)
    {
        $historial = $this->historial_comisiones ?? [];
        $historial[] = array_merge($comisionData, [
            'fecha_registro' => now(),
            'id_comision' => uniqid()
        ]);
        $this->historial_comisiones = $historial;

        // Actualizar contadores
        $this->comisiones_generadas += $comisionData['monto'] ?? 0;
        if (($comisionData['estado'] ?? 'pendiente') === 'pendiente') {
            $this->comisiones_pendientes += $comisionData['monto'] ?? 0;
        } else {
            $this->comisiones_pagadas += $comisionData['monto'] ?? 0;
        }

        return $this->save();
    }

    public function actualizarVentasReferido($montoVenta)
    {
        $this->total_ventas_referido += $montoVenta;
        return $this->save();
    }

    public function marcarComisionComoPagada($comisionId, $montoComision)
    {
        $this->comisiones_pendientes -= $montoComision;
        $this->comisiones_pagadas += $montoComision;

        // Actualizar en historial
        $historial = $this->historial_comisiones ?? [];
        foreach ($historial as &$comision) {
            if ($comision['id_comision'] === $comisionId) {
                $comision['estado'] = 'pagada';
                $comision['fecha_pago'] = now();
                break;
            }
        }
        $this->historial_comisiones = $historial;

        return $this->save();
    }

    public function agregarBonificacionEspecial($bonificacionData)
    {
        $bonificaciones = $this->bonificaciones_especiales ?? [];
        $bonificaciones[] = array_merge($bonificacionData, [
            'fecha' => now(),
            'id_bonificacion' => uniqid()
        ]);
        $this->bonificaciones_especiales = $bonificaciones;
        return $this->save();
    }

    public function desactivar($motivo = null)
    {
        $this->activo = false;
        $this->estado_referido = 'inactivo';

        if ($motivo) {
            $this->agregarNotaHistorial('Desactivado: ' . $motivo);
        }

        return $this->save();
    }

    private function agregarNotaHistorial($nota)
    {
        $historial = $this->historial_comisiones ?? [];
        $historial[] = [
            'tipo' => 'nota',
            'mensaje' => $nota,
            'fecha' => now(),
            'usuario_id' => auth()->id()
        ];
        $this->historial_comisiones = $historial;
    }
}