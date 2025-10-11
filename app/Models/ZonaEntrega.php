<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ZonaEntrega extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'zonas_entrega';

    protected $fillable = [
        'nombre',
        'descripcion',
        'ciudad',
        'departamento',
        'codigo_postal',
        'coordenadas',
        'precio_domicilio',
        'tiempo_entrega_estimado',
        'activa',
        'vendedores_asignados',
        'cobertura_barrios',
        'horarios_entrega',
        'restricciones',
        'configuracion_especial',
        'estadisticas',
        'observaciones'
    ];

    protected $casts = [
        'precio_domicilio' => 'decimal:2',
        'tiempo_entrega_estimado' => 'integer', // en minutos
        'activa' => 'boolean',
        'coordenadas' => 'array', // [lat, lng]
        'vendedores_asignados' => 'array',
        'cobertura_barrios' => 'array',
        'horarios_entrega' => 'array',
        'restricciones' => 'array',
        'configuracion_especial' => 'array',
        'estadisticas' => 'array',
        'observaciones' => 'array'
    ];

    // Relaciones
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'zona_entrega_id');
    }

    public function vendedores()
    {
        return $this->belongsToMany(User::class, null, 'zona_ids', 'vendedor_ids');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopePorCiudad($query, $ciudad)
    {
        return $query->where('ciudad', $ciudad);
    }

    public function scopePorDepartamento($query, $departamento)
    {
        return $query->where('departamento', $departamento);
    }

    public function scopeConDomicilio($query)
    {
        return $query->where('precio_domicilio', '>', 0);
    }

    public function scopeDomicilioGratis($query)
    {
        return $query->where('precio_domicilio', 0);
    }

    public function scopeEntregaRapida($query, $maxMinutos = 60)
    {
        return $query->where('tiempo_entrega_estimado', '<=', $maxMinutos);
    }

    public function scopeConVendedorAsignado($query, $vendedorId)
    {
        return $query->where('vendedores_asignados', 'elemMatch', ['vendedor_id' => $vendedorId]);
    }

    // Métodos auxiliares
    public function estaActiva(): bool
    {
        return $this->activa;
    }

    public function tieneDomicilioGratis(): bool
    {
        return $this->precio_domicilio == 0;
    }

    public function tiempoEntregaHoras(): float
    {
        return $this->tiempo_entrega_estimado / 60;
    }

    public function nombreCompleto(): string
    {
        return $this->nombre . ' - ' . $this->ciudad;
    }

    public function tieneVendedoresAsignados(): bool
    {
        return !empty($this->vendedores_asignados);
    }

    public function cantidadVendedoresAsignados(): int
    {
        return count($this->vendedores_asignados ?? []);
    }

    public function puedeEntregarEn($barrio): bool
    {
        $barrios = $this->cobertura_barrios ?? [];
        return in_array(strtolower($barrio), array_map('strtolower', $barrios));
    }

    public function estaDisponibleEn($dia, $hora = null): bool
    {
        $horarios = $this->horarios_entrega ?? [];

        if (empty($horarios)) {
            return true; // Si no hay restricciones de horario, está disponible
        }

        $diaActual = strtolower($dia);

        if (!isset($horarios[$diaActual])) {
            return false;
        }

        if (!$hora) {
            return $horarios[$diaActual]['activo'] ?? true;
        }

        $horario = $horarios[$diaActual];
        if (!($horario['activo'] ?? true)) {
            return false;
        }

        $horaInicio = $horario['hora_inicio'] ?? '00:00';
        $horaFin = $horario['hora_fin'] ?? '23:59';

        return $hora >= $horaInicio && $hora <= $horaFin;
    }

    public function distanciaAproximada($latitud, $longitud): ?float
    {
        if (empty($this->coordenadas) || count($this->coordenadas) < 2) {
            return null;
        }

        $latZona = $this->coordenadas[0];
        $lngZona = $this->coordenadas[1];

        // Fórmula haversine simplificada para distancia aproximada
        $radioTierra = 6371; // km
        $dLat = deg2rad($latitud - $latZona);
        $dLng = deg2rad($longitud - $lngZona);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($latZona)) * cos(deg2rad($latitud)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $radioTierra * $c;
    }

    // Métodos específicos de MongoDB
    public function asignarVendedor($vendedorData)
    {
        $vendedores = $this->vendedores_asignados ?? [];

        // Verificar si ya está asignado
        foreach ($vendedores as $vendedor) {
            if ($vendedor['vendedor_id'] === $vendedorData['vendedor_id']) {
                return false; // Ya está asignado
            }
        }

        $vendedores[] = array_merge($vendedorData, [
            'fecha_asignacion' => now(),
            'asignado_por' => auth()->id(),
            'activo' => true
        ]);

        $this->vendedores_asignados = $vendedores;
        return $this->save();
    }

    public function desasignarVendedor($vendedorId)
    {
        $vendedores = $this->vendedores_asignados ?? [];

        foreach ($vendedores as &$vendedor) {
            if ($vendedor['vendedor_id'] === $vendedorId) {
                $vendedor['activo'] = false;
                $vendedor['fecha_desasignacion'] = now();
                $vendedor['desasignado_por'] = auth()->id();
                break;
            }
        }

        $this->vendedores_asignados = $vendedores;
        return $this->save();
    }

    public function agregarBarrio($nombreBarrio, $codigoPostal = null)
    {
        $barrios = $this->cobertura_barrios ?? [];

        if (!in_array($nombreBarrio, $barrios)) {
            $barrios[] = $nombreBarrio;
            $this->cobertura_barrios = $barrios;

            // Agregar información adicional si es necesario
            if ($codigoPostal) {
                $configuracion = $this->configuracion_especial ?? [];
                $configuracion['barrios_codigos'][$nombreBarrio] = $codigoPostal;
                $this->configuracion_especial = $configuracion;
            }

            return $this->save();
        }

        return false;
    }

    public function quitarBarrio($nombreBarrio)
    {
        $barrios = $this->cobertura_barrios ?? [];
        $barrios = array_filter($barrios, function($barrio) use ($nombreBarrio) {
            return $barrio !== $nombreBarrio;
        });

        $this->cobertura_barrios = array_values($barrios);
        return $this->save();
    }

    public function configurarHorario($dia, $horaInicio, $horaFin, $activo = true)
    {
        $horarios = $this->horarios_entrega ?? [];
        $horarios[strtolower($dia)] = [
            'activo' => $activo,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'actualizado' => now()
        ];

        $this->horarios_entrega = $horarios;
        return $this->save();
    }

    public function agregarRestriccion($tipo, $descripcion, $parametros = [])
    {
        $restricciones = $this->restricciones ?? [];
        $restricciones[] = [
            'tipo' => $tipo,
            'descripcion' => $descripcion,
            'parametros' => $parametros,
            'fecha_creacion' => now(),
            'creado_por' => auth()->id(),
            'activa' => true,
            'id' => uniqid()
        ];

        $this->restricciones = $restricciones;
        return $this->save();
    }

    public function actualizarEstadisticas($nuevasEstadisticas)
    {
        $estadisticas = $this->estadisticas ?? [];
        $estadisticas[date('Y-m')] = array_merge(
            $estadisticas[date('Y-m')] ?? [],
            $nuevasEstadisticas,
            ['ultima_actualizacion' => now()]
        );

        $this->estadisticas = $estadisticas;
        return $this->save();
    }

    public function agregarObservacion($observacion, $tipo = 'general')
    {
        $observaciones = $this->observaciones ?? [];
        $observaciones[] = [
            'observacion' => $observacion,
            'tipo' => $tipo,
            'fecha' => now(),
            'usuario_id' => auth()->id(),
            'id' => uniqid()
        ];

        $this->observaciones = $observaciones;
        return $this->save();
    }
}