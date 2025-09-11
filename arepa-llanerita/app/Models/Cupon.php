<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'valor',
        'monto_minimo',
        'usos_maximos',
        'usos_realizados',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'categorias_aplicables',
        'productos_aplicables',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'monto_minimo' => 'decimal:2',
            'usos_maximos' => 'integer',
            'usos_realizados' => 'integer',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activo' => 'boolean',
            'categorias_aplicables' => 'json',
            'productos_aplicables' => 'json',
        ];
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now());
    }

    public function scopeDisponibles($query)
    {
        return $query->where(function($q) {
            $q->whereNull('usos_maximos')
              ->orWhereColumn('usos_realizados', '<', 'usos_maximos');
        });
    }

    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', strtoupper($codigo));
    }

    public function scopePorcentaje($query)
    {
        return $query->where('tipo', 'porcentaje');
    }

    public function scopeMontoFijo($query)
    {
        return $query->where('tipo', 'monto_fijo');
    }

    // Métodos auxiliares
    public function estaActivo(): bool
    {
        return $this->activo;
    }

    public function estaVigente(): bool
    {
        $hoy = now()->format('Y-m-d');
        return $this->fecha_inicio <= $hoy && $this->fecha_fin >= $hoy;
    }

    public function tieneUsosDisponibles(): bool
    {
        if (!$this->usos_maximos) {
            return true;
        }
        
        return $this->usos_realizados < $this->usos_maximos;
    }

    public function puedeUsarse(): bool
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

    public function calcularDescuento(float $montoTotal): float
    {
        if (!$this->puedeUsarse() || $montoTotal < $this->monto_minimo) {
            return 0;
        }

        if ($this->esPorcentaje()) {
            return ($montoTotal * $this->valor) / 100;
        }

        return min($this->valor, $montoTotal);
    }

    public function aplicableAProducto(int $productoId): bool
    {
        if (!$this->productos_aplicables) {
            return true;
        }

        return in_array($productoId, $this->productos_aplicables);
    }

    public function aplicableACategoria(int $categoriaId): bool
    {
        if (!$this->categorias_aplicables) {
            return true;
        }

        return in_array($categoriaId, $this->categorias_aplicables);
    }

    public function puedeAplicarseACarrito(array $items, float $montoTotal): array
    {
        $resultado = [
            'puede_aplicarse' => false,
            'motivo' => '',
            'descuento' => 0,
        ];

        if (!$this->puedeUsarse()) {
            $resultado['motivo'] = 'Cupón no válido o vencido';
            return $resultado;
        }

        if ($montoTotal < $this->monto_minimo) {
            $resultado['motivo'] = "Monto mínimo requerido: $" . number_format($this->monto_minimo, 0);
            return $resultado;
        }

        // Verificar si se aplica a productos específicos
        if ($this->productos_aplicables || $this->categorias_aplicables) {
            $aplicable = false;
            foreach ($items as $item) {
                if ($this->aplicableAProducto($item['producto_id']) || 
                    $this->aplicableACategoria($item['categoria_id'] ?? 0)) {
                    $aplicable = true;
                    break;
                }
            }
            
            if (!$aplicable) {
                $resultado['motivo'] = 'Cupón no aplicable a estos productos';
                return $resultado;
            }
        }

        $resultado['puede_aplicarse'] = true;
        $resultado['descuento'] = $this->calcularDescuento($montoTotal);
        
        return $resultado;
    }

    public function usar(): void
    {
        $this->increment('usos_realizados');
    }

    public function usosRestantes(): ?int
    {
        if (!$this->usos_maximos) {
            return null;
        }
        
        return max(0, $this->usos_maximos - $this->usos_realizados);
    }

    public function porcentajeUsos(): float
    {
        if (!$this->usos_maximos) {
            return 0;
        }
        
        return ($this->usos_realizados / $this->usos_maximos) * 100;
    }

    // Métodos estáticos
    public static function buscarPorCodigo(string $codigo): ?self
    {
        return self::where('codigo', strtoupper(trim($codigo)))->first();
    }

    public static function validarCodigo(string $codigo, array $items = [], float $montoTotal = 0): array
    {
        $cupon = self::buscarPorCodigo($codigo);
        
        if (!$cupon) {
            return [
                'valido' => false,
                'motivo' => 'Código de cupón no encontrado',
                'descuento' => 0,
            ];
        }
        
        $validacion = $cupon->puedeAplicarseACarrito($items, $montoTotal);
        
        return [
            'valido' => $validacion['puede_aplicarse'],
            'motivo' => $validacion['motivo'],
            'descuento' => $validacion['descuento'],
            'cupon' => $cupon,
        ];
    }
}
