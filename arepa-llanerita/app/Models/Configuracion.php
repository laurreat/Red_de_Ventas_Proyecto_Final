<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'descripcion',
        'categoria',
        'editable',
    ];

    protected function casts(): array
    {
        return [
            'editable' => 'boolean',
        ];
    }

    // Scopes
    public function scopeEditables($query)
    {
        return $query->where('editable', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorClave($query, $clave)
    {
        return $query->where('clave', $clave);
    }

    // Métodos auxiliares
    public function esEditable(): bool
    {
        return $this->editable;
    }

    public function valorTipado()
    {
        return match($this->tipo) {
            'boolean' => filter_var($this->valor, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->valor,
            'float', 'number' => (float) $this->valor,
            'array', 'json' => json_decode($this->valor, true),
            default => $this->valor
        };
    }

    // Métodos estáticos para facilitar el uso
    public static function obtener(string $clave, $default = null)
    {
        $config = self::where('clave', $clave)->first();
        
        if (!$config) {
            return $default;
        }
        
        return $config->valorTipado();
    }

    public static function establecer(string $clave, $valor, string $tipo = 'string', string $descripcion = '', string $categoria = 'general'): self
    {
        $valorString = is_array($valor) || is_object($valor) ? json_encode($valor) : (string) $valor;
        
        return self::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valorString,
                'tipo' => $tipo,
                'descripcion' => $descripcion,
                'categoria' => $categoria,
            ]
        );
    }

    public static function incrementar(string $clave, int $cantidad = 1): bool
    {
        $config = self::where('clave', $clave)->first();
        
        if (!$config || !in_array($config->tipo, ['integer', 'number', 'float'])) {
            return false;
        }
        
        $valorActual = $config->valorTipado();
        $config->update(['valor' => $valorActual + $cantidad]);
        
        return true;
    }

    // Configuraciones comunes del sistema
    public static function nombreEmpresa(): string
    {
        return self::obtener('nombre_empresa', 'Arepa la Llanerita');
    }

    public static function porcentajeIva(): float
    {
        return self::obtener('iva_porcentaje', 19.0);
    }

    public static function pedidoMinimo(): float
    {
        return self::obtener('pedido_minimo', 15000.0);
    }

    public static function costoEnvioBase(): float
    {
        return self::obtener('costo_envio_base', 5000.0);
    }

    public static function comisionVentaDirecta(): float
    {
        return self::obtener('comision_venta_directa', 15.0);
    }

    public static function comisionReferidoRegistro(): float
    {
        return self::obtener('comision_referido_registro', 5000.0);
    }

    public static function comisionReferidoPrimeraCompra(): float
    {
        return self::obtener('comision_referido_primera_compra', 10.0);
    }
}
