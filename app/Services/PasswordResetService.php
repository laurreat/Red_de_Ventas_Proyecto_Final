<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetService
{
    private const TOKENS_DIRECTORY = 'password_resets';
    private const TOKEN_EXPIRY_MINUTES = 60;

    /**
     * Crear un token de restablecimiento
     */
    public function createToken(string $email): string
    {
        // Asegurar que el directorio existe
        $this->ensureDirectoryExists();
        
        // Limpiar tokens expirados primero
        $deletedExpired = $this->cleanExpiredTokens();
        \Log::info('Tokens expirados limpiados', ['count' => $deletedExpired]);

        // Eliminar TODOS los tokens anteriores del usuario
        $deletedUser = $this->deleteTokensForEmail($email);
        \Log::info('Tokens anteriores del usuario eliminados', [
            'email' => $email,
            'count' => $deletedUser
        ]);

        // Generar nuevo token ÚNICO
        $token = Str::random(64);

        // Guardar token en archivo
        $data = [
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()->toISOString(),
            'expires_at' => Carbon::now()->addMinutes(self::TOKEN_EXPIRY_MINUTES)->toISOString(),
            'ip' => request()->ip() ?? 'unknown',
            'user_agent' => request()->userAgent() ?? 'unknown'
        ];

        $filename = $this->getTokenFilename($token);
        $saved = Storage::put($filename, json_encode($data, JSON_PRETTY_PRINT));
        
        if (!$saved) {
            \Log::error('No se pudo guardar el token', [
                'email' => $email,
                'filename' => $filename
            ]);
            throw new \Exception('No se pudo crear el token de restablecimiento');
        }

        \Log::info('Nuevo token creado exitosamente', [
            'email' => $email,
            'token' => substr($token, 0, 10) . '...',
            'filename' => $filename,
            'expires_at' => $data['expires_at']
        ]);

        return $token;
    }

    /**
     * Verificar si un token es válido
     */
    public function verifyToken(string $email, string $token): bool
    {
        $filename = $this->getTokenFilename($token);

        \Log::info('Verificando token', [
            'email' => $email,
            'token' => substr($token, 0, 10) . '...',
            'filename' => $filename,
            'exists' => Storage::exists($filename)
        ]);

        if (!Storage::exists($filename)) {
            \Log::warning('Token no encontrado', [
                'email' => $email,
                'filename' => $filename
            ]);
            return false;
        }

        try {
            $data = json_decode(Storage::get($filename), true);

            if (!$data || !isset($data['email']) || $data['email'] !== $email) {
                \Log::warning('Token no coincide con el email', [
                    'email_solicitado' => $email,
                    'email_token' => $data['email'] ?? 'N/A'
                ]);
                return false;
            }

            // Verificar expiración
            $expiresAt = Carbon::parse($data['expires_at']);
            $now = Carbon::now();
            
            \Log::info('Verificando expiración', [
                'expires_at' => $expiresAt->toDateTimeString(),
                'now' => $now->toDateTimeString(),
                'is_expired' => $expiresAt->isPast()
            ]);
            
            if ($expiresAt->isPast()) {
                // Token expirado, eliminarlo
                \Log::warning('Token expirado', [
                    'email' => $email,
                    'expired_at' => $expiresAt->toDateTimeString()
                ]);
                Storage::delete($filename);
                return false;
            }

            \Log::info('Token válido', [
                'email' => $email,
                'remaining_minutes' => $now->diffInMinutes($expiresAt)
            ]);

            return true;
            
        } catch (\Exception $e) {
            \Log::error('Error al verificar token', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Consumir un token (eliminarlo después de usarlo)
     */
    public function consumeToken(string $email, string $token): bool
    {
        if (!$this->verifyToken($email, $token)) {
            return false;
        }

        $filename = $this->getTokenFilename($token);
        Storage::delete($filename);

        // Limpiar otros tokens del usuario
        $this->deleteTokensForEmail($email);

        return true;
    }

    /**
     * Eliminar todos los tokens de un email
     */
    public function deleteTokensForEmail(string $email): int
    {
        $deleted = 0;
        $files = Storage::files(self::TOKENS_DIRECTORY);

        foreach ($files as $file) {
            if (Storage::exists($file)) {
                try {
                    $data = json_decode(Storage::get($file), true);
                    if ($data && isset($data['email']) && $data['email'] === $email) {
                        Storage::delete($file);
                        $deleted++;
                        \Log::info('Token anterior eliminado', [
                            'email' => $email,
                            'file' => basename($file)
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error al procesar archivo de token', [
                        'file' => $file,
                        'error' => $e->getMessage()
                    ]);
                    // Eliminar archivo corrupto
                    Storage::delete($file);
                    $deleted++;
                }
            }
        }
        
        return $deleted;
    }

    /**
     * Limpiar tokens expirados
     */
    public function cleanExpiredTokens(): int
    {
        $files = Storage::files(self::TOKENS_DIRECTORY);
        $deletedCount = 0;
        $now = Carbon::now();

        foreach ($files as $file) {
            if (Storage::exists($file)) {
                $data = json_decode(Storage::get($file), true);

                if ($data && isset($data['expires_at'])) {
                    $expiresAt = Carbon::parse($data['expires_at']);
                    if ($expiresAt->isPast()) {
                        Storage::delete($file);
                        $deletedCount++;
                    }
                } else {
                    // Archivo malformado, eliminarlo
                    Storage::delete($file);
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }

    /**
     * Obtener el nombre del archivo para un token
     */
    private function getTokenFilename(string $token): string
    {
        return self::TOKENS_DIRECTORY . '/' . $token . '.json';
    }

    /**
     * Crear directorio si no existe
     */
    public function ensureDirectoryExists(): void
    {
        if (!Storage::exists(self::TOKENS_DIRECTORY)) {
            Storage::makeDirectory(self::TOKENS_DIRECTORY);
        }
    }
}