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
        // Limpiar tokens expirados
        $this->cleanExpiredTokens();

        // Eliminar tokens anteriores del usuario
        $this->deleteTokensForEmail($email);

        // Generar nuevo token
        $token = Str::random(64);

        // Guardar token en archivo
        $data = [
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()->toISOString(),
            'expires_at' => Carbon::now()->addMinutes(self::TOKEN_EXPIRY_MINUTES)->toISOString()
        ];

        $filename = $this->getTokenFilename($token);
        Storage::put($filename, json_encode($data));

        return $token;
    }

    /**
     * Verificar si un token es válido
     */
    public function verifyToken(string $email, string $token): bool
    {
        $filename = $this->getTokenFilename($token);

        if (!Storage::exists($filename)) {
            return false;
        }

        $data = json_decode(Storage::get($filename), true);

        if (!$data || $data['email'] !== $email) {
            return false;
        }

        // Verificar expiración
        $expiresAt = Carbon::parse($data['expires_at']);
        if ($expiresAt->isPast()) {
            // Token expirado, eliminarlo
            Storage::delete($filename);
            return false;
        }

        return true;
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
    public function deleteTokensForEmail(string $email): void
    {
        $files = Storage::files(self::TOKENS_DIRECTORY);

        foreach ($files as $file) {
            if (Storage::exists($file)) {
                $data = json_decode(Storage::get($file), true);
                if ($data && isset($data['email']) && $data['email'] === $email) {
                    Storage::delete($file);
                }
            }
        }
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