<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Pedido;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Estadísticas del usuario
        $stats = [
            'pedidos_gestionados' => Pedido::where('gestionado_por', $user->_id)->count(),
            'usuarios_creados' => User::where('creado_por', $user->_id)->count(),
            'fecha_registro' => $user->created_at,
            'ultimo_acceso' => $user->last_login_at ?? $user->updated_at,
            'rol_actual' => $user->rol,
            'estado_cuenta' => $user->activo ? 'Activa' : 'Inactiva'
        ];

        // Actividad reciente
        $actividadReciente = [
            'pedidos_recientes' => Pedido::where('gestionado_por', $user->_id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'usuarios_recientes' => User::where('creado_por', $user->_id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
        ];

        // Configuración de notificaciones del usuario
        $notificaciones = [
            'email_pedidos' => $user->notif_email_pedidos ?? true,
            'email_usuarios' => $user->notif_email_usuarios ?? true,
            'email_sistema' => $user->notif_email_sistema ?? true,
            'sms_urgente' => $user->notif_sms_urgente ?? false,
            'push_browser' => $user->notif_push_browser ?? true
        ];

        return view('admin.perfil.index', compact('user', 'stats', 'actividadReciente', 'notificaciones'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->_id . ',_id',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Manejar subida de avatar
            if ($request->hasFile('avatar')) {
                // Eliminar avatar anterior si existe
                if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                    Storage::delete('public/avatars/' . $user->avatar);
                }

                // Subir nuevo avatar
                $avatarName = time() . '_' . $user->_id . '.' . $request->avatar->extension();
                $request->avatar->storeAs('public/avatars', $avatarName);
                $user->avatar = $avatarName;
            }

            // Actualizar datos del usuario
            $user->update([
                'name' => $request->name,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'bio' => $request->bio,
                'avatar' => $user->avatar
            ]);

            return redirect()->route('admin.perfil.index')
                ->with('success', 'Perfil actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return redirect()->route('admin.perfil.index')
                ->with('success', 'Contraseña actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar contraseña: ' . $e->getMessage());
        }
    }

    public function updateNotifications(Request $request)
    {
        $user = auth()->user();

        try {
            $user->update([
                'notif_email_pedidos' => $request->has('email_pedidos'),
                'notif_email_usuarios' => $request->has('email_usuarios'),
                'notif_email_sistema' => $request->has('email_sistema'),
                'notif_sms_urgente' => $request->has('sms_urgente'),
                'notif_push_browser' => $request->has('push_browser')
            ]);

            return redirect()->route('admin.perfil.index')
                ->with('success', 'Preferencias de notificaciones actualizadas.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar notificaciones: ' . $e->getMessage());
        }
    }

    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'perfil_publico' => 'boolean',
            'mostrar_email' => 'boolean',
            'mostrar_telefono' => 'boolean',
            'mostrar_ultima_conexion' => 'boolean'
        ]);

        $user = auth()->user();

        try {
            $user->update([
                'perfil_publico' => $request->has('perfil_publico'),
                'mostrar_email' => $request->has('mostrar_email'),
                'mostrar_telefono' => $request->has('mostrar_telefono'),
                'mostrar_ultima_conexion' => $request->has('mostrar_ultima_conexion')
            ]);

            return redirect()->route('admin.perfil.index')
                ->with('success', 'Configuración de privacidad actualizada.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar privacidad: ' . $e->getMessage());
        }
    }

    public function deleteAvatar()
    {
        $user = auth()->user();

        try {
            if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            $user->update(['avatar' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar avatar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadData()
    {
        $user = auth()->user();

        try {
            // Recopilar todos los datos del usuario
            $userData = [
                'informacion_personal' => [
                    'nombre' => $user->name,
                    'apellidos' => $user->apellidos,
                    'email' => $user->email,
                    'telefono' => $user->telefono,
                    'direccion' => $user->direccion,
                    'fecha_nacimiento' => $user->fecha_nacimiento,
                    'fecha_registro' => $user->created_at,
                    'rol' => $user->rol,
                    'estado' => $user->activo ? 'Activo' : 'Inactivo'
                ],
                'actividad' => [
                    'ultimo_acceso' => $user->last_login_at,
                    'pedidos_gestionados' => Pedido::where('gestionado_por', $user->_id)->count(),
                    'usuarios_creados' => User::where('creado_por', $user->_id)->count()
                ],
                'configuracion' => [
                    'notificaciones' => [
                        'email_pedidos' => $user->notif_email_pedidos ?? true,
                        'email_usuarios' => $user->notif_email_usuarios ?? true,
                        'email_sistema' => $user->notif_email_sistema ?? true,
                        'sms_urgente' => $user->notif_sms_urgente ?? false,
                        'push_browser' => $user->notif_push_browser ?? true
                    ],
                    'privacidad' => [
                        'perfil_publico' => $user->perfil_publico ?? false,
                        'mostrar_email' => $user->mostrar_email ?? false,
                        'mostrar_telefono' => $user->mostrar_telefono ?? false,
                        'mostrar_ultima_conexion' => $user->mostrar_ultima_conexion ?? false
                    ]
                ],
                'metadatos' => [
                    'fecha_exportacion' => now()->toISOString(),
                    'version' => '1.0'
                ]
            ];

            $json = json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $filename = 'mis_datos_' . $user->_id . '_' . now()->format('Y-m-d') . '.json';

            return response($json)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar exportación: ' . $e->getMessage());
        }
    }

    public function activity()
    {
        $user = auth()->user();

        // Actividad detallada de los últimos 30 días
        $actividad = [
            'pedidos' => Pedido::where('gestionado_por', $user->_id)
                ->where('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            'usuarios_creados' => User::where('creado_por', $user->_id)
                ->where('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            'resumen' => [
                'accesos_ultimo_mes' => $this->contarAccesosUltimoMes($user),
                'promedio_accesos_diarios' => $this->promedioAccesosDiarios($user),
                'tiempo_sesion_promedio' => $this->tiempoSesionPromedio($user)
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $actividad
        ]);
    }

    // Métodos privados auxiliares

    private function contarAccesosUltimoMes($user)
    {
        // Simulación - en un entorno real, esto vendría de una tabla de logs de acceso
        return rand(20, 60);
    }

    private function promedioAccesosDiarios($user)
    {
        // Simulación
        return round(rand(1, 5), 1);
    }

    private function tiempoSesionPromedio($user)
    {
        // Simulación
        return rand(30, 120) . ' minutos';
    }
}