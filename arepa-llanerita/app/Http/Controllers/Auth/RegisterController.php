<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'cedula' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'telefono' => ['required', 'string', 'max:20'],
            'direccion' => ['nullable', 'string'],
            'ciudad' => ['required', 'string', 'max:100'],
            'departamento' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'codigo_referido_usado' => ['nullable', 'string'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'cedula.required' => 'La cédula es obligatoria.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'departamento.required' => 'El departamento es obligatorio.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ]);

        // Validación personalizada para cédula única en MongoDB
        $validator->after(function ($validator) use ($data) {
            if (isset($data['cedula'])) {
                $existingUser = User::where('cedula', $data['cedula'])->first();
                if ($existingUser) {
                    $validator->errors()->add('cedula', 'Esta cédula ya está registrada.');
                }
            }

            if (isset($data['email'])) {
                $existingUser = User::where('email', $data['email'])->first();
                if ($existingUser) {
                    $validator->errors()->add('email', 'Este correo electrónico ya está registrado.');
                }
            }
        });

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        try {
            DB::beginTransaction();

            $user = $this->create($request->all());

            DB::commit();

            $this->guard()->login($user);

            return $this->registered($request, $user)
                        ?: redirect($this->redirectPath())
                            ->with('success', '¡Registro exitoso! Bienvenido al sistema.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Verificar si es un error de duplicado específico de MongoDB
            if (str_contains($e->getMessage(), 'duplicate') || str_contains($e->getMessage(), 'E11000')) {
                if (str_contains($e->getMessage(), 'email')) {
                    throw ValidationException::withMessages([
                        'email' => ['Este correo electrónico ya está registrado.'],
                    ]);
                }

                if (str_contains($e->getMessage(), 'cedula')) {
                    throw ValidationException::withMessages([
                        'cedula' => ['Esta cédula ya está registrada.'],
                    ]);
                }
            }

            Log::error('Error en registro de usuario', [
                'error' => $e->getMessage(),
                'data' => $request->except('password', 'password_confirmation'),
                'trace' => $e->getTraceAsString()
            ]);

            throw ValidationException::withMessages([
                'email' => ['Error al registrar el usuario. Por favor, intente nuevamente.'],
            ]);
        }
    }

    protected function create(array $data)
    {
        // Buscar el referidor si existe código de referido
        $referidoPor = null;
        if (!empty($data['codigo_referido_usado'])) {
            $referidor = User::where('codigo_referido', $data['codigo_referido_usado'])->first();
            if ($referidor) {
                $referidoPor = $referidor->_id;
            }
        }

        return User::create([
            'name' => $data['name'],
            'apellidos' => $data['apellidos'],
            'cedula' => $data['cedula'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'telefono' => $data['telefono'],
            'direccion' => $data['direccion'] ?? null,
            'ciudad' => $data['ciudad'],
            'departamento' => $data['departamento'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'rol' => 'cliente', // Por defecto los registros son clientes
            'activo' => true,
            'referido_por' => $referidoPor,
            'codigo_referido' => $this->generarCodigoReferido(),
        ]);
    }

    /**
     * Generar código de referido único
     */
    private function generarCodigoReferido(): string
    {
        do {
            $codigo = 'REF' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('codigo_referido', $codigo)->exists());
        
        return $codigo;
    }
}
