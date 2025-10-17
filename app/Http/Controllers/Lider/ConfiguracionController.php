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
        ]);

        $configuraciones = $user->configuraciones ?? [];
        $configuraciones = array_merge($configuraciones, $request->only([
            'notificaciones_email',
            'notificaciones_push',
        ]));

        $user->update(['configuraciones' => $configuraciones]);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }

    /**
     * Actualizar configuración en tiempo real (AJAX)
     */
    public function updateRealtime(Request $request)
    {
        $user = Auth::user();

        $field = $request->input('field');
        $value = $request->input('value');

        // Validar campo permitido
        $allowedFields = ['notificaciones_email', 'notificaciones_push'];

        if (!in_array($field, $allowedFields)) {
            return response()->json([
                'success' => false,
                'message' => 'Campo no permitido'
            ], 400);
        }

        // Obtener configuraciones actuales
        $configuraciones = $user->configuraciones ?? [];

        // Actualizar el campo específico
        $configuraciones[$field] = (bool) $value;

        // Guardar
        $user->update(['configuraciones' => $configuraciones]);

        return response()->json([
            'success' => true,
            'message' => 'Configuración guardada automáticamente'
        ]);
    }

    /**
     * Restablecer configuración a valores predeterminados
     */
    public function reset(Request $request)
    {
        $user = Auth::user();

        $configuraciones = [
            'notificaciones_email' => true,
            'notificaciones_push' => true,
        ];

        $user->update(['configuraciones' => $configuraciones]);

        return response()->json([
            'success' => true,
            'message' => 'Configuración restablecida correctamente'
        ]);
    }
}
