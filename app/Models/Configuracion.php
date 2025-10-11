<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Configuracion extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'categoria',
        'descripcion',
        'es_publica',
        'es_editable',
        'validaciones',
        'opciones_disponibles',
        'valor_por_defecto',
        'historial_cambios',
        'metadatos',
        'dependencias',
        'grupo',
        'orden'
    ];

    protected $casts = [
        'valor' => 'array', // Para flexibilidad de tipos
        'es_publica' => 'boolean',
        'es_editable' => 'boolean',
        'validaciones' => 'array',
        'opciones_disponibles' => 'array',
        'valor_por_defecto' => 'array',
        'historial_cambios' => 'array',
        'metadatos' => 'array',
        'dependencias' => 'array',
        'orden' => 'integer'
    ];

    // Scopes
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorGrupo($query, $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    public function scopePublicas($query)
    {
        return $query->where('es_publica', true);
    }

    public function scopeEditables($query)
    {
        return $query->where('es_editable', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('grupo', 'asc')->orderBy('orden', 'asc');
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('clave', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }

    // Métodos auxiliares
    public function esPublica(): bool
    {
        return $this->es_publica;
    }

    public function esEditable(): bool
    {
        return $this->es_editable;
    }

    public function getValorComoString(): string
    {
        if (is_array($this->valor)) {
            return $this->valor['valor'] ?? '';
        }
        return (string) $this->valor;
    }

    public function getValorComoBool(): bool
    {
        if (is_array($this->valor)) {
            return (bool) ($this->valor['valor'] ?? false);
        }
        return (bool) $this->valor;
    }

    public function getValorComoNumero(): float
    {
        if (is_array($this->valor)) {
            return (float) ($this->valor['valor'] ?? 0);
        }
        return (float) $this->valor;
    }

    public function getValorComoArray(): array
    {
        if (is_array($this->valor)) {
            return $this->valor['valor'] ?? [];
        }
        return [];
    }

    public function getValorFormateado()
    {
        switch ($this->tipo) {
            case 'boolean':
                return $this->getValorComoBool();
            case 'integer':
                return (int) $this->getValorComoNumero();
            case 'float':
            case 'decimal':
                return $this->getValorComoNumero();
            case 'array':
            case 'json':
                return $this->getValorComoArray();
            case 'string':
            default:
                return $this->getValorComoString();
        }
    }

    public function tieneHistorial(): bool
    {
        return !empty($this->historial_cambios);
    }

    public function ultimoCambio(): ?array
    {
        $historial = $this->historial_cambios ?? [];
        return end($historial) ?: null;
    }

    public function cantidadCambios(): int
    {
        return count($this->historial_cambios ?? []);
    }

    public function tieneDependencias(): bool
    {
        return !empty($this->dependencias);
    }

    public function tieneOpciones(): bool
    {
        return !empty($this->opciones_disponibles);
    }

    // Métodos específicos de MongoDB
    public function actualizarValor($nuevoValor, $usuarioId = null, $motivo = null)
    {
        $valorAnterior = $this->valor;

        // Validar el nuevo valor si hay validaciones definidas
        if (!$this->validarValor($nuevoValor)) {
            return false;
        }

        // Estructurar el valor según el tipo
        $valorEstructurado = $this->estructurarValor($nuevoValor);

        // Agregar al historial
        $this->agregarCambioAlHistorial($valorAnterior, $valorEstructurado, $usuarioId, $motivo);

        // Actualizar el valor
        $this->valor = $valorEstructurado;

        return $this->save();
    }

    private function validarValor($valor): bool
    {
        $validaciones = $this->validaciones ?? [];

        foreach ($validaciones as $validacion) {
            switch ($validacion['tipo']) {
                case 'requerido':
                    if (empty($valor) && $validacion['valor']) {
                        return false;
                    }
                    break;

                case 'min_length':
                    if (is_string($valor) && strlen($valor) < $validacion['valor']) {
                        return false;
                    }
                    break;

                case 'max_length':
                    if (is_string($valor) && strlen($valor) > $validacion['valor']) {
                        return false;
                    }
                    break;

                case 'min_value':
                    if (is_numeric($valor) && $valor < $validacion['valor']) {
                        return false;
                    }
                    break;

                case 'max_value':
                    if (is_numeric($valor) && $valor > $validacion['valor']) {
                        return false;
                    }
                    break;

                case 'regex':
                    if (is_string($valor) && !preg_match($validacion['valor'], $valor)) {
                        return false;
                    }
                    break;

                case 'opciones':
                    if (!in_array($valor, $this->opciones_disponibles ?? [])) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    private function estructurarValor($valor): array
    {
        return [
            'valor' => $valor,
            'tipo' => $this->tipo,
            'fecha_actualizacion' => now(),
            'usuario_actualizacion' => auth()->id()
        ];
    }

    private function agregarCambioAlHistorial($valorAnterior, $valorNuevo, $usuarioId, $motivo)
    {
        $historial = $this->historial_cambios ?? [];
        $historial[] = [
            'valor_anterior' => $valorAnterior,
            'valor_nuevo' => $valorNuevo,
            'fecha_cambio' => now(),
            'usuario_id' => $usuarioId ?? auth()->id(),
            'motivo' => $motivo,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'id_cambio' => uniqid()
        ];

        $this->historial_cambios = $historial;
    }

    public function restaurarValor($cambioId)
    {
        $historial = $this->historial_cambios ?? [];

        foreach ($historial as $cambio) {
            if ($cambio['id_cambio'] === $cambioId) {
                $this->valor = $cambio['valor_anterior'];
                $this->agregarCambioAlHistorial(
                    $this->valor,
                    $cambio['valor_anterior'],
                    auth()->id(),
                    "Restauración del cambio: {$cambioId}"
                );
                return $this->save();
            }
        }

        return false;
    }

    public function agregarMetadato($clave, $valor)
    {
        $metadatos = $this->metadatos ?? [];
        $metadatos[$clave] = [
            'valor' => $valor,
            'fecha_agregado' => now(),
            'agregado_por' => auth()->id()
        ];
        $this->metadatos = $metadatos;
        return $this->save();
    }

    public function quitarMetadato($clave)
    {
        $metadatos = $this->metadatos ?? [];
        unset($metadatos[$clave]);
        $this->metadatos = $metadatos;
        return $this->save();
    }

    public function agregarDependencia($configuracionClave, $tipoRelacion = 'depende_de')
    {
        $dependencias = $this->dependencias ?? [];
        $dependencias[] = [
            'configuracion_clave' => $configuracionClave,
            'tipo_relacion' => $tipoRelacion,
            'fecha_agregado' => now(),
            'agregado_por' => auth()->id()
        ];
        $this->dependencias = $dependencias;
        return $this->save();
    }

    public function quitarDependencia($configuracionClave)
    {
        $dependencias = $this->dependencias ?? [];
        $dependencias = array_filter($dependencias, function($dep) use ($configuracionClave) {
            return $dep['configuracion_clave'] !== $configuracionClave;
        });
        $this->dependencias = array_values($dependencias);
        return $this->save();
    }

    public function restablecerValorPorDefecto()
    {
        if ($this->valor_por_defecto) {
            return $this->actualizarValor(
                $this->valor_por_defecto['valor'],
                auth()->id(),
                'Restablecimiento a valor por defecto'
            );
        }
        return false;
    }

    // Métodos estáticos para configuraciones comunes
    public static function obtener($clave, $valorPorDefecto = null)
    {
        $configuracion = self::where('clave', $clave)->first();
        return $configuracion ? $configuracion->getValorFormateado() : $valorPorDefecto;
    }

    public static function establecer($clave, $valor, $usuarioId = null, $motivo = null)
    {
        $configuracion = self::where('clave', $clave)->first();
        if ($configuracion) {
            return $configuracion->actualizarValor($valor, $usuarioId, $motivo);
        }
        return false;
    }

    public static function obtenerPorCategoria($categoria)
    {
        return self::porCategoria($categoria)->ordenadas()->get();
    }

    public static function obtenerPorGrupo($grupo)
    {
        return self::porGrupo($grupo)->ordenadas()->get();
    }
}