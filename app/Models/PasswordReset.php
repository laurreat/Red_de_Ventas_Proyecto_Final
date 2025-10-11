<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PasswordReset extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'password_resets';

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    protected $dates = [
        'created_at'
    ];

    public $timestamps = false;

    /**
     * Verificar si el token ha expirado
     */
    public function isExpired(): bool
    {
        return $this->created_at->addMinutes(60)->isPast();
    }

    /**
     * Obtener un token válido por email
     */
    public static function getValidToken(string $email, string $token): ?self
    {
        $reset = self::where('email', $email)
                    ->where('token', $token)
                    ->first();

        if ($reset && !$reset->isExpired()) {
            return $reset;
        }

        return null;
    }

    /**
     * Limpiar tokens expirados
     */
    public static function cleanExpiredTokens(): int
    {
        return self::where('created_at', '<', now()->subHours(1))->delete();
    }
}