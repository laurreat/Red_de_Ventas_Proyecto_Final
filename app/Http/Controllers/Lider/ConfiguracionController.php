<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $configuraciones = [
            'notificaciones_email' => $user->configuraciones['notificaciones_email'] ?? true,
            'notificaciones_push' => $user->configuraciones['notificaciones_push'] ?? true,
            'mostrar_rendimiento' => $user->configuraciones['mostrar_rendimiento'] ?? true,
            'tema_dashboard' => $user->configuraciones['tema_dashboard'] ?? 'claro',
            'zona_horaria' => $user->configuraciones['zona_horaria'] ?? 'America/Bogota',
        ];

        return view('lider.configuracion.index', compact('user', 'configuraciones'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'notificaciones_email' => 'boolean',
            'notificaciones_push' => 'boolean',
            'mostrar_rendimiento' => 'boolean',
            'tema_dashboard' => 'string|in:claro,oscuro',
            'zona_horaria' => 'string',
        ]);

        $configuraciones = $user->configuraciones ?? [];
        $configuraciones = array_merge($configuraciones, $request->only([
            'notificaciones_email',
            'notificaciones_push',
            'mostrar_rendimiento',
            'tema_dashboard',
            'zona_horaria'
        ]));

        $user->update(['configuraciones' => $configuraciones]);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
