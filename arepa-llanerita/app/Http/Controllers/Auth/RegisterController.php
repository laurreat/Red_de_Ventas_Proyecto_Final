<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'cedula' => ['required', 'string', 'max:20', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'telefono' => ['required', 'string', 'max:20'],
            'direccion' => ['nullable', 'string'],
            'ciudad' => ['required', 'string', 'max:100'],
            'departamento' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'codigo_referido_usado' => ['nullable', 'string', 'exists:users,codigo_referido'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Buscar el referidor si existe código de referido
        $referidoPor = null;
        if (!empty($data['codigo_referido_usado'])) {
            $referidor = User::where('codigo_referido', $data['codigo_referido_usado'])->first();
            if ($referidor) {
                $referidoPor = $referidor->id;
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
