<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Mostrar el formulario de restablecimiento
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->email;

        if (!$token || !$email) {
            return redirect()->route('password.request')
                ->with('error', 'Enlace de restablecimiento inválido.');
        }

        // Verificar que el token existe y es válido
        $passwordResetService = new PasswordResetService();
        if (!$passwordResetService->verifyToken($email, $token)) {
            return redirect()->route('password.request')
                ->with('error', 'El enlace de restablecimiento ha expirado o es inválido.');
        }

        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Restablecer la contraseña
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            // Verificar token
            $passwordResetService = new PasswordResetService();
            if (!$passwordResetService->verifyToken($request->email, $request->token)) {
                throw ValidationException::withMessages([
                    'email' => ['El enlace de restablecimiento ha expirado o es inválido.'],
                ]);
            }

            // Buscar usuario
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['No se encontró un usuario con este correo electrónico.'],
                ]);
            }

            // Actualizar contraseña
            $user->password = Hash::make($request->password);
            $user->save();

            // Consumir el token (eliminarlo)
            $passwordResetService->consumeToken($request->email, $request->token);

            Log::info('Contraseña restablecida exitosamente', [
                'user_id' => $user->_id,
                'email' => $user->email
            ]);

            // Loguear automáticamente al usuario
            Auth::login($user);

            return redirect($this->redirectTo)
                ->with('status', 'Contraseña restablecida exitosamente. Has sido conectado automáticamente.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al restablecer contraseña', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            throw ValidationException::withMessages([
                'email' => ['Error al restablecer la contraseña. Por favor, intente nuevamente.'],
            ]);
        }
    }
}
