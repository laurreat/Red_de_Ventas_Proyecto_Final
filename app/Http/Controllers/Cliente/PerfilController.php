<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Pedido;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Mostrar perfil del cliente
     */
    public function index()
    {
        $user = Auth::user();
        
        // Estadísticas del usuario
        $stats = [
            'total_pedidos' => Pedido::where('user_id', $user->_id)->count(),
            'pedidos_entregados' => Pedido::where('user_id', $user->_id)->where('estado', 'entregado')->count(),
            'total_gastado' => to_float(Pedido::where('user_id', $user->_id)
                ->whereIn('estado', ['confirmado', 'en_preparacion', 'enviado', 'entregado'])
                ->sum('total_final')),
            'fecha_registro' => $user->created_at,
        ];
        
        return view('cliente.perfil.index', compact('user', 'stats'));
    }

    /**
     * Actualizar información personal
     */
    public function actualizarInformacion(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'cedula' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'ciudad' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre no puede superar los 255 caracteres',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->name = $request->name;
        $user->apellidos = $request->apellidos;
        $user->telefono = $request->telefono;
        $user->cedula = $request->cedula;
        $user->direccion = $request->direccion;
        $user->ciudad = $request->ciudad;
        $user->departamento = $request->departamento;
        
        $user->save();

        return back()->with('success', 'Información personal actualizada correctamente');
    }

    /**
     * Actualizar foto de perfil
     */
    public function actualizarFoto(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'foto.required' => 'Debes seleccionar una imagen',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La imagen debe ser tipo: jpeg, png, jpg o gif',
            'foto.max' => 'La imagen no puede superar los 2MB',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            // Eliminar foto anterior si existe
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // Guardar nueva foto
            $path = $request->file('foto')->store('perfiles', 'public');
            $user->foto = $path;
            $user->save();

            return back()->with('success', 'Foto de perfil actualizada correctamente');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar foto de perfil: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la foto. Intenta nuevamente.');
        }
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'password_actual' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password_actual.required' => 'Debes ingresar tu contraseña actual',
            'password.required' => 'Debes ingresar la nueva contraseña',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Verificar contraseña actual
        if (!Hash::check($request->password_actual, $user->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta']);
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Contraseña cambiada correctamente');
    }

    /**
     * Eliminar foto de perfil
     */
    public function eliminarFoto()
    {
        $user = Auth::user();
        
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->foto = null;
        $user->save();

        return back()->with('success', 'Foto de perfil eliminada correctamente');
    }
}
