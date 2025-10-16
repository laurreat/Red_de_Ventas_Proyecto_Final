<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Mostrar el formulario para solicitar enlace de restablecimiento
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Enviar enlace de restablecimiento por correo
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255']
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            // Buscar usuario
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // No revelar si el email existe o no por seguridad
                return back()->with('status', 'Si el correo está registrado, recibirás un enlace de restablecimiento.');
            }

            // Crear token usando el servicio
            $passwordResetService = new PasswordResetService();
            $passwordResetService->ensureDirectoryExists();
            $token = $passwordResetService->createToken($request->email);

            // Enviar correo
            try {
                $this->sendResetEmail($user, $token);
                $mailSent = true;
            } catch (\Exception $mailError) {
                Log::error('Error enviando correo', [
                    'user_id' => $user->_id,
                    'email' => $user->email,
                    'error' => $mailError->getMessage()
                ]);
                $mailSent = false;
            }

            return back()->with('status', 'Si el correo está registrado, recibirás un enlace de restablecimiento en tu bandeja de entrada.');

        } catch (\Exception $e) {
            Log::error('Error enviando correo de restablecimiento', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            throw ValidationException::withMessages([
                'email' => ['Error al procesar la solicitud. Por favor, intente nuevamente.'],
            ]);
        }
    }

    /**
     * Enviar el correo de restablecimiento
     */
    private function sendResetEmail(User $user, string $token)
    {
        $resetUrl = url("/password/reset/{$token}?email=" . urlencode($user->email));

        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'token' => $token
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name . ' ' . $user->apellidos)
                        ->subject('Restablecimiento de Contraseña - Arepa la Llanerita');
            });

        } catch (\Exception $e) {
            Log::error('Error enviando correo', [
                'user_id' => $user->_id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
