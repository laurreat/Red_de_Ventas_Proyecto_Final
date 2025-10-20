<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Mostrar configuración del cliente
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('cliente.configuracion.index', compact('user'));
    }

    /**
     * Actualizar preferencias de notificaciones
     */
    public function actualizarNotificaciones(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'notificaciones_email' => 'boolean',
            'notificaciones_push' => 'boolean',
            'notificaciones_pedidos' => 'boolean',
            'notificaciones_promociones' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->configuracion = array_merge($user->configuracion ?? [], [
            'notificaciones' => [
                'email' => $request->boolean('notificaciones_email'),
                'push' => $request->boolean('notificaciones_push'),
                'pedidos' => $request->boolean('notificaciones_pedidos'),
                'promociones' => $request->boolean('notificaciones_promociones'),
            ],
            'actualizado_en' => now()
        ]);

        $user->save();

        return back()->with('success', 'Configuración de notificaciones actualizada correctamente');
    }

    /**
     * Actualizar preferencias de privacidad
     */
    public function actualizarPrivacidad(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'perfil_publico' => 'boolean',
            'mostrar_email' => 'boolean',
            'mostrar_telefono' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->configuracion = array_merge($user->configuracion ?? [], [
            'privacidad' => [
                'perfil_publico' => $request->boolean('perfil_publico'),
                'mostrar_email' => $request->boolean('mostrar_email'),
                'mostrar_telefono' => $request->boolean('mostrar_telefono'),
            ],
            'actualizado_en' => now()
        ]);

        $user->save();

        return back()->with('success', 'Configuración de privacidad actualizada correctamente');
    }

    /**
     * Actualizar preferencias generales
     */
    public function actualizarGenerales(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'idioma' => 'required|in:es,en',
            'zona_horaria' => 'required|string',
            'tema' => 'required|in:light,dark,auto',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->configuracion = array_merge($user->configuracion ?? [], [
            'general' => [
                'idioma' => $request->idioma,
                'zona_horaria' => $request->zona_horaria,
                'tema' => $request->tema,
            ],
            'actualizado_en' => now()
        ]);

        $user->save();

        return back()->with('success', 'Configuración general actualizada correctamente');
    }
}
