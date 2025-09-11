<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'titulo',
        'mensaje',
        'tipo',
        'leida',
        'url',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'leida' => 'boolean',
            'metadata' => 'json',
        ];
    }

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }

    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('user_id', $usuarioId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Métodos auxiliares
    public function estaLeida(): bool
    {
        return $this->leida;
    }

    public function marcarComoLeida(): void
    {
        $this->update(['leida' => true]);
    }

    public function marcarComoNoLeida(): void
    {
        $this->update(['leida' => false]);
    }

    public function esInfo(): bool
    {
        return $this->tipo === 'info';
    }

    public function esExito(): bool
    {
        return $this->tipo === 'success';
    }

    public function esAdvertencia(): bool
    {
        return $this->tipo === 'warning';
    }

    public function esError(): bool
    {
        return $this->tipo === 'error';
    }

    public function tieneUrl(): bool
    {
        return !empty($this->url);
    }

    public function tiempoTranscurrido(): string
    {
        $diff = $this->created_at->diffForHumans();
        return $diff;
    }

    public function iconoTipo(): string
    {
        return match($this->tipo) {
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'error' => 'times-circle',
            'info' => 'info-circle',
            default => 'bell'
        };
    }

    public function colorTipo(): string
    {
        return match($this->tipo) {
            'success' => 'green',
            'warning' => 'yellow',
            'error' => 'red',
            'info' => 'blue',
            default => 'gray'
        };
    }

    // Métodos estáticos
    public static function crear(int $usuarioId, string $titulo, string $mensaje, string $tipo = 'info', ?string $url = null, array $metadata = []): self
    {
        return self::create([
            'user_id' => $usuarioId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'url' => $url,
            'metadata' => $metadata,
        ]);
    }

    public static function marcarTodasComoLeidas(int $usuarioId): void
    {
        self::where('user_id', $usuarioId)
            ->where('leida', false)
            ->update(['leida' => true]);
    }

    public static function contarNoLeidas(int $usuarioId): int
    {
        return self::where('user_id', $usuarioId)
                  ->where('leida', false)
                  ->count();
    }

    public static function limpiarAntiguas(int $diasAntiguedad = 30): int
    {
        return self::where('created_at', '<', now()->subDays($diasAntiguedad))
                  ->where('leida', true)
                  ->delete();
    }
}
