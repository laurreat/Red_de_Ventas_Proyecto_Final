<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Validar login con protección NoSQL injection
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Prevenir inyección NoSQL
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
                // Prevenir caracteres MongoDB peligrosos
                'regex:/^[^${}[\]]+$/'
            ]
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.regex' => 'Formato de correo inválido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.regex' => 'La contraseña contiene caracteres no permitidos.'
        ]);
    }

    /**
     * Sanitizar credenciales antes de autenticación
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');

        // Sanitizar email
        $credentials['email'] = filter_var($credentials['email'], FILTER_SANITIZE_EMAIL);

        // Remover caracteres peligrosos para MongoDB
        $credentials['email'] = preg_replace('/[^a-zA-Z0-9.@_-]/', '', $credentials['email']);

        return $credentials;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        try {
            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if (method_exists($this, 'hasTooManyLoginAttempts') &&
                $this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }

                return $this->sendLoginResponse($request);
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);

        } catch (\Exception $e) {
            // Log el error pero no revelar detalles al usuario
            Log::error('Error durante el intento de login', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Incrementar intentos fallidos
            $this->incrementLoginAttempts($request);

            // Siempre devolver el mismo mensaje de error genérico
            return $this->sendFailedLoginResponse($request);
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }
}
