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
                Log::info('Solicitud de restablecimiento para email no registrado', [
                    'email' => $request->email
                ]);
                return back()->with('status', 'Si el correo está registrado, recibirás un enlace de restablecimiento.');
            }

            // Crear token usando el servicio (SIEMPRE genera uno nuevo)
            $passwordResetService = new PasswordResetService();
            
            Log::info('Iniciando creación de token', [
                'user_id' => $user->_id,
                'email' => $user->email
            ]);
            
            $token = $passwordResetService->createToken($request->email);
            
            Log::info('Token generado', [
                'user_id' => $user->_id,
                'email' => $user->email,
                'token_length' => strlen($token),
                'token_preview' => substr($token, 0, 10) . '...'
            ]);

            // Enviar correo
            try {
                $this->sendResetEmail($user, $token);
                
                Log::info('Correo de restablecimiento enviado exitosamente', [
                    'user_id' => $user->_id,
                    'email' => $user->email,
                    'token_preview' => substr($token, 0, 10) . '...'
                ]);

                return back()->with('status', 'Se ha enviado un enlace de restablecimiento a tu correo electrónico. Por favor, revisa tu bandeja de entrada. El enlace es válido por 1 hora.');

            } catch (\Exception $mailError) {
                Log::error('Error enviando correo de restablecimiento', [
                    'user_id' => $user->_id,
                    'email' => $user->email,
                    'error' => $mailError->getMessage(),
                    'trace' => $mailError->getTraceAsString()
                ]);

                // Mensaje más específico sobre el error de correo
                throw ValidationException::withMessages([
                    'email' => ['No se pudo enviar el correo electrónico. Por favor, verifica tu conexión a internet e intenta nuevamente. Si el problema persiste, contacta al soporte técnico.'],
                ]);
            }

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error general al procesar solicitud de restablecimiento', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw ValidationException::withMessages([
                'email' => ['Error al procesar la solicitud. Por favor, verifica tu conexión a internet e intenta nuevamente.'],
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
