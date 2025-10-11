<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Cupon extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cupones';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'porcentaje_descuento',
        'monto_descuento',
        'descripcion',
        'fecha_inicio',
        'fecha_vencimiento',
        'limite_usos',
        'usos_actuales',
        'activo',
        'aplicable_a',
        'productos_aplicables',
        'categorias_aplicables',
        'usuarios_aplicables',
        'monto_minimo_compra',
        'monto_maximo_descuento',
        'restricciones',
        'condiciones_especiales',
        'historial_usos',
        'created_by',
        'estadisticas'
    ];

    protected $casts = [
        'porcentaje_descuento' => 'decimal:2',
        'monto_descuento' => 'decimal:2',
        'fecha_inicio' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'limite_usos' => 'integer',
        'usos_actuales' => 'integer',
        'activo' => 'boolean',
        'monto_minimo_compra' => 'decimal:2',
        'monto_maximo_descuento' => 'decimal:2',
        'productos_aplicables' => 'array',
        'categorias_aplicables' => 'array',
        'usuarios_aplicables' => 'array',
        'restricciones' => 'array',
        'condiciones_especiales' => 'array',
        'historial_usos' => 'array',
        'estadisticas' => 'array'
    ];

    // Relaciones
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('fecha_inicio', '<=', now())
                    ->where('fecha_vencimiento', '>=', now());
    }

    public function scopeDisponibles($query)
    {
        return $query->activos()
                    ->vigentes()
                    ->where(function($q) {
                        $q->whereNull('limite_usos')
                          ->orWhereRaw('usos_actuales < limite_usos');
                    });
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorcentaje($query)
    {
        return $query->where('tipo', 'porcentaje');
    }

    public function scopeMontoFijo($query)
    {
        return $query->where('tipo', 'monto_fijo');
    }

    public function scopeEnvioGratis($query)
    {
        return $query->where('tipo', 'envio_gratis');
    }

    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', strtoupper($codigo));
    }

    public function scopeAplicableAProducto($query, $productoId)
    {
        return $query->where(function($q) use ($productoId) {
            $q->where('aplicable_a', 'todos')
              ->orWhere('productos_aplicables', 'elemMatch', ['producto_id' => $productoId]);
        });
    }

    public function scopeAplicableACategoria($query, $categoriaId)
    {
        return $query->where(function($q) use ($categoriaId) {
            $q->where('aplicable_a', 'todos')
              ->orWhere('categorias_aplicables', $categoriaId);
        });
    }

    public function scopeAplicableAUsuario($query, $usuarioId)
    {
        return $query->where(function($q) use ($usuarioId) {
            $q->where('aplicable_a', 'todos')
              ->orWhere('usuarios_aplicables', $usuarioId);
        });
    }

    // Métodos auxiliares
    public function estaActivo(): bool
    {
        return $this->activo;
    }

    public function estaVigente(): bool
    {
        $ahora = now();
        return $ahora >= $this->fecha_inicio && $ahora <= $this->fecha_vencimiento;
    }

    public function tieneUsosDisponibles(): bool
    {
        if (!$this->limite_usos) {
            return true; // Sin límite
        }
        return $this->usos_actuales < $this->limite_usos;
    }

    public function estaDisponible(): bool
    {
        return $this->estaActivo() && $this->estaVigente() && $this->tieneUsosDisponibles();
    }

    public function esPorcentaje(): bool
    {
        return $this->tipo === 'porcentaje';
    }

    public function esMontoFijo(): bool
    {
        return $this->tipo === 'monto_fijo';
    }

    public function esEnvioGratis(): bool
    {
        return $this->tipo === 'envio_gratis';
    }

    public function diasParaVencer(): int
    {
        return now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function porcentajeUsado(): float
    {
        if (!$this->limite_usos) {
            return 0;
        }
        return ($this->usos_actuales / $this->limite_usos) * 100;
    }

    public function usosRestantes(): ?int
    {
        if (!$this->limite_usos) {
            return null; // Ilimitado
        }
        return max(0, $this->limite_usos - $this->usos_actuales);
    }

    // Métodos de validación y aplicación
    public function puedeAplicarseA($item, $tipoItem = 'producto'): bool
    {
        if (!$this->estaDisponible()) {
            return false;
        }

        switch ($this->aplicable_a) {
            case 'todos':
                return true;

            case 'productos':
                if ($tipoItem === 'producto') {
                    $productosAplicables = array_column($this->productos_aplicables ?? [], 'producto_id');
                    return in_array($item, $productosAplicables);
                }
                return false;

            case 'categorias':
                if ($tipoItem === 'categoria') {
                    return in_array($item, $this->categorias_aplicables ?? []);
                }
                return false;

            case 'usuarios':
                if ($tipoItem === 'usuario') {
                    return in_array($item, $this->usuarios_aplicables ?? []);
                }
                return false;

            default:
                return false;
        }
    }

    public function calcularDescuento($montoBase, $cantidadItems = 1): float
    {
        if (!$this->estaDisponible()) {
            return 0;
        }

        $descuento = 0;

        switch ($this->tipo) {
            case 'porcentaje':
                $descuento = ($montoBase * $this->porcentaje_descuento) / 100;
                break;

            case 'monto_fijo':
                $descuento = $this->monto_descuento;
                break;

            case 'envio_gratis':
                // El descuento se aplicaría al costo de envío, no al monto base
                $descuento = 0;
                break;
        }

        // Aplicar límite máximo si existe
        if ($this->monto_maximo_descuento && $descuento > $this->monto_maximo_descuento) {
            $descuento = $this->monto_maximo_descuento;
        }

        // No puede ser mayor al monto base
        return min($descuento, $montoBase);
    }

    public function validarCondiciones($datosCompra): array
    {
        $errores = [];

        // Validar monto mínimo
        if ($this->monto_minimo_compra && $datosCompra['total'] < $this->monto_minimo_compra) {
            $errores[] = "El monto mínimo de compra es $" . number_format($this->monto_minimo_compra, 2);
        }

        // Validar restricciones personalizadas
        $restricciones = $this->restricciones ?? [];
        foreach ($restricciones as $restriccion) {
            switch ($restriccion['tipo']) {
                case 'primera_compra':
                    if ($restriccion['valor'] && !$datosCompra['es_primera_compra']) {
                        $errores[] = "Este cupón solo es válido para la primera compra";
                    }
                    break;

                case 'dia_semana':
                    $diasPermitidos = $restriccion['valor'];
                    if (!in_array(now()->dayOfWeek, $diasPermitidos)) {
                        $errores[] = "Este cupón no es válido en el día actual";
                    }
                    break;

                case 'horario':
                    $horaActual = now()->format('H:i');
                    if ($horaActual < $restriccion['hora_inicio'] || $horaActual > $restriccion['hora_fin']) {
                        $errores[] = "Este cupón no es válido en el horario actual";
                    }
                    break;

                case 'cantidad_maxima':
                    if ($datosCompra['cantidad_items'] > $restriccion['valor']) {
                        $errores[] = "Este cupón no es válido para más de {$restriccion['valor']} items";
                    }
                    break;
            }
        }

        return $errores;
    }

    // Métodos específicos de MongoDB
    public function usar($datosUso)
    {
        if (!$this->estaDisponible()) {
            return false;
        }

        // Agregar al historial
        $historial = $this->historial_usos ?? [];
        $historial[] = array_merge($datosUso, [
            'fecha_uso' => now(),
            'id_uso' => uniqid()
        ]);
        $this->historial_usos = $historial;

        // Incrementar contador
        $this->usos_actuales++;

        // Actualizar estadísticas
        $this->actualizarEstadisticas($datosUso);

        return $this->save();
    }

    private function actualizarEstadisticas($datosUso)
    {
        $estadisticas = $this->estadisticas ?? [];
        $mes = now()->format('Y-m');

        if (!isset($estadisticas[$mes])) {
            $estadisticas[$mes] = [
                'usos' => 0,
                'monto_total_descuentos' => 0,
                'usuarios_unicos' => []
            ];
        }

        $estadisticas[$mes]['usos']++;
        $estadisticas[$mes]['monto_total_descuentos'] += $datosUso['descuento_aplicado'] ?? 0;

        $usuarioId = $datosUso['usuario_id'] ?? null;
        if ($usuarioId && !in_array($usuarioId, $estadisticas[$mes]['usuarios_unicos'])) {
            $estadisticas[$mes]['usuarios_unicos'][] = $usuarioId;
        }

        $this->estadisticas = $estadisticas;
    }

    public function clonar($nuevoCodigo = null, $usuarioCreadorId = null)
    {
        $datos = $this->toArray();
        unset($datos['_id'], $datos['created_at'], $datos['updated_at']);

        $datos['codigo'] = $nuevoCodigo ?? $this->codigo . '_COPIA_' . time();
        $datos['usos_actuales'] = 0;
        $datos['historial_usos'] = [];
        $datos['estadisticas'] = [];
        $datos['created_by'] = $usuarioCreadorId ?? auth()->id();

        return self::create($datos);
    }

    public function extenderVigencia($dias)
    {
        $this->fecha_vencimiento = $this->fecha_vencimiento->addDays($dias);
        return $this->save();
    }

    public function desactivar($motivo = null)
    {
        $this->activo = false;

        if ($motivo) {
            $restricciones = $this->restricciones ?? [];
            $restricciones[] = [
                'tipo' => 'desactivacion',
                'motivo' => $motivo,
                'fecha' => now(),
                'usuario_id' => auth()->id()
            ];
            $this->restricciones = $restricciones;
        }

        return $this->save();
    }

    public function agregarProductoAplicable($productoId, $productoData = null)
    {
        $productos = $this->productos_aplicables ?? [];
        $productos[] = [
            'producto_id' => $productoId,
            'producto_data' => $productoData,
            'fecha_agregado' => now()
        ];
        $this->productos_aplicables = $productos;
        return $this->save();
    }

    public function quitarProductoAplicable($productoId)
    {
        $productos = $this->productos_aplicables ?? [];
        $productos = array_filter($productos, function($producto) use ($productoId) {
            return $producto['producto_id'] !== $productoId;
        });
        $this->productos_aplicables = array_values($productos);
        return $this->save();
    }

    // Métodos estáticos
    public static function validarCodigo($codigo, $datosCompra = null): array
    {
        $cupon = self::porCodigo($codigo)->first();

        if (!$cupon) {
            return ['valido' => false, 'mensaje' => 'Cupón no encontrado'];
        }

        if (!$cupon->estaDisponible()) {
            return ['valido' => false, 'mensaje' => 'Cupón no disponible'];
        }

        if ($datosCompra) {
            $errores = $cupon->validarCondiciones($datosCompra);
            if (!empty($errores)) {
                return ['valido' => false, 'mensaje' => implode('. ', $errores)];
            }
        }

        return ['valido' => true, 'cupon' => $cupon];
    }
}