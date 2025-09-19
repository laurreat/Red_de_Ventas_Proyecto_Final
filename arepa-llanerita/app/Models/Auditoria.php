<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Auditoria extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'auditoria';

    protected $fillable = [
        'modelo',
        'modelo_id',
        'accion',
        'usuario_id',
        'usuario_data',
        'datos_anteriores',
        'datos_nuevos',
        'cambios_detectados',
        'ip_address',
        'user_agent',
        'url',
        'metodo_http',
        'session_id',
        'tabla_afectada',
        'contexto_adicional',
        'nivel_criticidad',
        'categoria',
        'tags',
        'datos_request',
        'tiempo_ejecucion'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'cambios_detectados' => 'array',
        'usuario_data' => 'array',
        'contexto_adicional' => 'array',
        'tags' => 'array',
        'datos_request' => 'array',
        'tiempo_ejecucion' => 'float'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    public function scopePorModelo($query, $modelo)
    {
        return $query->where('modelo', $modelo);
    }

    public function scopePorModeloId($query, $modeloId)
    {
        return $query->where('modelo_id', $modeloId);
    }

    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeCreaciones($query)
    {
        return $query->where('accion', 'created');
    }

    public function scopeActualizaciones($query)
    {
        return $query->where('accion', 'updated');
    }

    public function scopeEliminaciones($query)
    {
        return $query->where('accion', 'deleted');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeUltimoMes($query)
    {
        return $query->where('created_at', '>=', now()->subMonth());
    }

    public function scopePorNivelCriticidad($query, $nivel)
    {
        return $query->where('nivel_criticidad', $nivel);
    }

    public function scopeCriticos($query)
    {
        return $query->where('nivel_criticidad', 'critico');
    }

    public function scopeAltos($query)
    {
        return $query->where('nivel_criticidad', 'alto');
    }

    public function scopeMedios($query)
    {
        return $query->where('nivel_criticidad', 'medio');
    }

    public function scopeBajos($query)
    {
        return $query->where('nivel_criticidad', 'bajo');
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeConTag($query, $tag)
    {
        return $query->where('tags', $tag);
    }

    public function scopePorIP($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    // Métodos auxiliares
    public function esCreacion(): bool
    {
        return $this->accion === 'created';
    }

    public function esActualizacion(): bool
    {
        return $this->accion === 'updated';
    }

    public function esEliminacion(): bool
    {
        return $this->accion === 'deleted';
    }

    public function esCritico(): bool
    {
        return $this->nivel_criticidad === 'critico';
    }

    public function esAlto(): bool
    {
        return $this->nivel_criticidad === 'alto';
    }

    public function esMedio(): bool
    {
        return $this->nivel_criticidad === 'medio';
    }

    public function esBajo(): bool
    {
        return $this->nivel_criticidad === 'bajo';
    }

    public function tieneCambios(): bool
    {
        return !empty($this->cambios_detectados);
    }

    public function cantidadCambios(): int
    {
        return count($this->cambios_detectados ?? []);
    }

    public function tieneTag($tag): bool
    {
        return in_array($tag, $this->tags ?? []);
    }

    public function obtenerCambiosCampo($campo): ?array
    {
        $cambios = $this->cambios_detectados ?? [];
        return $cambios[$campo] ?? null;
    }

    public function formatearTiempoEjecucion(): string
    {
        if ($this->tiempo_ejecucion < 1) {
            return round($this->tiempo_ejecucion * 1000, 2) . ' ms';
        }
        return round($this->tiempo_ejecucion, 3) . ' s';
    }

    public function obtenerResumen(): string
    {
        $usuario = $this->usuario_data['name'] ?? 'Sistema';
        $accion = $this->accion;
        $modelo = $this->modelo;

        switch ($accion) {
            case 'created':
                return "Usuario {$usuario} creó un registro en {$modelo}";
            case 'updated':
                $campos = count($this->cambios_detectados ?? []);
                return "Usuario {$usuario} actualizó {$campos} campo(s) en {$modelo}";
            case 'deleted':
                return "Usuario {$usuario} eliminó un registro de {$modelo}";
            default:
                return "Usuario {$usuario} realizó acción {$accion} en {$modelo}";
        }
    }

    // Métodos específicos de MongoDB
    public function embederDatosUsuario()
    {
        if ($this->usuario_id && !$this->usuario_data) {
            $usuario = User::find($this->usuario_id);
            if ($usuario) {
                $this->usuario_data = [
                    '_id' => $usuario->_id,
                    'name' => $usuario->name,
                    'apellidos' => $usuario->apellidos,
                    'email' => $usuario->email,
                    'rol' => $usuario->rol
                ];
            }
        }
        return $this->save();
    }

    public function agregarContexto($clave, $valor)
    {
        $contexto = $this->contexto_adicional ?? [];
        $contexto[$clave] = [
            'valor' => $valor,
            'fecha_agregado' => now(),
            'agregado_por' => auth()->id()
        ];
        $this->contexto_adicional = $contexto;
        return $this->save();
    }

    public function agregarTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->tags = $tags;
            return $this->save();
        }
        return false;
    }

    public function quitarTag($tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, function($t) use ($tag) {
            return $t !== $tag;
        });
        $this->tags = array_values($tags);
        return $this->save();
    }

    public function marcarComoRevisado($usuarioId = null, $observaciones = null)
    {
        $this->agregarContexto('revision', [
            'revisado' => true,
            'fecha_revision' => now(),
            'revisado_por' => $usuarioId ?? auth()->id(),
            'observaciones' => $observaciones
        ]);

        $this->agregarTag('revisado');

        return $this->save();
    }

    public function estaRevisado(): bool
    {
        return $this->tieneTag('revisado');
    }

    public function detectarCambiosSensibles(): array
    {
        $camposSensibles = [
            'password', 'email', 'telefono', 'cedula', 'rol',
            'precio', 'stock', 'total', 'estado', 'activo'
        ];

        $cambiosSensibles = [];
        $cambios = $this->cambios_detectados ?? [];

        foreach ($cambios as $campo => $datosCambio) {
            if (in_array($campo, $camposSensibles)) {
                $cambiosSensibles[$campo] = $datosCambio;
            }
        }

        return $cambiosSensibles;
    }

    public function tieneCambiosSensibles(): bool
    {
        return !empty($this->detectarCambiosSensibles());
    }

    // Métodos estáticos para crear auditorías
    public static function registrarCreacion($modelo, $modeloId, $datosNuevos, $contextAdicional = [])
    {
        return self::create([
            'modelo' => class_basename($modelo),
            'modelo_id' => $modeloId,
            'accion' => 'created',
            'usuario_id' => auth()->id(),
            'datos_nuevos' => $datosNuevos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'metodo_http' => request()->method(),
            'session_id' => session()->getId(),
            'tabla_afectada' => $modelo->getTable(),
            'contexto_adicional' => $contextAdicional,
            'nivel_criticidad' => 'medio',
            'categoria' => 'crud',
            'datos_request' => request()->except(['password', 'password_confirmation'])
        ]);
    }

    public static function registrarActualizacion($modelo, $modeloId, $datosAnteriores, $datosNuevos, $contextAdicional = [])
    {
        $cambios = self::detectarCambios($datosAnteriores, $datosNuevos);

        return self::create([
            'modelo' => class_basename($modelo),
            'modelo_id' => $modeloId,
            'accion' => 'updated',
            'usuario_id' => auth()->id(),
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'cambios_detectados' => $cambios,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'metodo_http' => request()->method(),
            'session_id' => session()->getId(),
            'tabla_afectada' => $modelo->getTable(),
            'contexto_adicional' => $contextAdicional,
            'nivel_criticidad' => self::determinarNivelCriticidad($cambios),
            'categoria' => 'crud',
            'datos_request' => request()->except(['password', 'password_confirmation'])
        ]);
    }

    public static function registrarEliminacion($modelo, $modeloId, $datosAnteriores, $contextAdicional = [])
    {
        return self::create([
            'modelo' => class_basename($modelo),
            'modelo_id' => $modeloId,
            'accion' => 'deleted',
            'usuario_id' => auth()->id(),
            'datos_anteriores' => $datosAnteriores,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'metodo_http' => request()->method(),
            'session_id' => session()->getId(),
            'tabla_afectada' => $modelo->getTable(),
            'contexto_adicional' => $contextAdicional,
            'nivel_criticidad' => 'alto',
            'categoria' => 'crud',
            'datos_request' => request()->except(['password', 'password_confirmation'])
        ]);
    }

    private static function detectarCambios($datosAnteriores, $datosNuevos): array
    {
        $cambios = [];

        foreach ($datosNuevos as $campo => $valorNuevo) {
            $valorAnterior = $datosAnteriores[$campo] ?? null;

            if ($valorAnterior !== $valorNuevo) {
                $cambios[$campo] = [
                    'anterior' => $valorAnterior,
                    'nuevo' => $valorNuevo,
                    'tipo_cambio' => self::determinarTipoCambio($valorAnterior, $valorNuevo)
                ];
            }
        }

        return $cambios;
    }

    private static function determinarTipoCambio($valorAnterior, $valorNuevo): string
    {
        if (is_null($valorAnterior) && !is_null($valorNuevo)) {
            return 'creacion';
        }

        if (!is_null($valorAnterior) && is_null($valorNuevo)) {
            return 'eliminacion';
        }

        if (is_bool($valorAnterior) || is_bool($valorNuevo)) {
            return 'cambio_booleano';
        }

        if (is_numeric($valorAnterior) && is_numeric($valorNuevo)) {
            return $valorNuevo > $valorAnterior ? 'incremento' : 'decremento';
        }

        return 'modificacion';
    }

    private static function determinarNivelCriticidad($cambios): string
    {
        $camposCriticos = ['password', 'email', 'rol', 'activo'];
        $camposAltos = ['precio', 'stock', 'total', 'estado'];

        foreach ($cambios as $campo => $datosCambio) {
            if (in_array($campo, $camposCriticos)) {
                return 'critico';
            }
            if (in_array($campo, $camposAltos)) {
                return 'alto';
            }
        }

        return count($cambios) > 5 ? 'medio' : 'bajo';
    }
}