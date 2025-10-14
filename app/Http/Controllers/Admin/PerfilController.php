<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'pedidos_como_cliente' => Pedido::where('user_id', $user->_id)->count(),
            'pedidos_como_vendedor' => Pedido::where('vendedor_id', $user->_id)->count(),
            'total_referidos' => $user->total_referidos ?? 0,
            'fecha_registro' => $user->created_at,
            'ultimo_acceso' => $user->last_login_at ?? $user->updated_at,
            'rol_actual' => $user->rol ?? 'cliente',
            'estado_cuenta' => $user->activo ? 'Activa' : 'Inactiva'
        ];

        // Actividad reciente - pedidos como cliente
        $pedidosRecientesCliente = Pedido::where('user_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Actividad reciente - pedidos como vendedor
        $pedidosRecientesVendedor = Pedido::where('vendedor_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Usuarios referidos (si puede tener referidos)
        $usuariosReferidos = collect();
        if (in_array($user->rol, ['administrador', 'lider', 'vendedor'])) {
            $usuariosReferidos = User::where('referido_por', $user->_id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        $actividadReciente = [
            'pedidos_recientes' => $pedidosRecientesCliente->concat($pedidosRecientesVendedor)
                                                          ->sortByDesc('created_at')
                                                          ->take(5),
            'usuarios_recientes' => $usuariosReferidos
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
                if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                    Storage::disk('public')->delete('avatars/' . $user->avatar);
                }

                // Subir nuevo avatar
                $avatarName = time() . '_' . $user->_id . '.' . $request->avatar->extension();
                $path = $request->avatar->storeAs('avatars', $avatarName, 'public');

                // Debug: verificar que se guardó correctamente
                \Log::info('Avatar guardado', [
                    'user_id' => $user->_id,
                    'filename' => $avatarName,
                    'path' => $path,
                    'exists' => Storage::disk('public')->exists('avatars/' . $avatarName),
                    'url' => asset('storage/avatars/' . $avatarName)
                ]);

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
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
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
            // Recopilar datos completos del usuario
            $pedidosCliente = Pedido::where('user_id', $user->_id)->get();
            $pedidosVendedor = Pedido::where('vendedor_id', $user->_id)->get();
            $referidos = User::where('referido_por', $user->_id)->get();

            $data = [
                'user' => $user,
                'stats' => [
                    'pedidos_como_cliente' => $pedidosCliente->count(),
                    'pedidos_como_vendedor' => $pedidosVendedor->count(),
                    'total_referidos' => $referidos->count(),
                    'total_gastado' => $pedidosCliente->sum('total_final'),
                    'total_vendido' => $pedidosVendedor->sum('total_final'),
                    'comisiones_ganadas' => $user->comisiones_ganadas ?? 0,
                    'comisiones_disponibles' => $user->comisiones_disponibles ?? 0,
                    'fecha_registro' => $user->created_at,
                    'ultimo_acceso' => $user->last_login_at ?? $user->updated_at,
                ],
                'pedidos_recientes' => $pedidosCliente->concat($pedidosVendedor)
                                                     ->sortByDesc('created_at')
                                                     ->take(10),
                'referidos_recientes' => $referidos->sortByDesc('created_at')->take(10),
                'fecha_generacion' => now(),
            ];

            $pdf = PDF::loadView('admin.perfil.pdf', $data);
            $pdf->setPaper('A4', 'portrait');

            $filename = 'perfil_' . ($user->name ?? 'usuario') . '_' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error al generar PDF del perfil: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    public function activity()
    {
        try {
            $user = auth()->user();

            // Obtener pedidos donde el usuario sea cliente o vendedor
            $pedidosComoCliente = Pedido::where('user_id', $user->_id)
                ->where('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'desc')
                ->take(25)
                ->get()
                ->map(function($pedido) {
                    return [
                        'id' => (string)$pedido->_id,
                        'numero_pedido' => $pedido->numero_pedido ?? 'N/A',
                        'estado' => $pedido->estado ?? 'pendiente',
                        'total_final' => $pedido->total_final ?? 0,
                        'created_at' => $pedido->created_at ? $pedido->created_at->toISOString() : null,
                        'tipo' => 'cliente'
                    ];
                });

            $pedidosComoVendedor = Pedido::where('vendedor_id', $user->_id)
                ->where('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'desc')
                ->take(25)
                ->get()
                ->map(function($pedido) {
                    return [
                        'id' => (string)$pedido->_id,
                        'numero_pedido' => $pedido->numero_pedido ?? 'N/A',
                        'estado' => $pedido->estado ?? 'pendiente',
                        'total_final' => $pedido->total_final ?? 0,
                        'created_at' => $pedido->created_at ? $pedido->created_at->toISOString() : null,
                        'tipo' => 'vendedor'
                    ];
                });

            // Obtener referidos si es admin, líder o vendedor
            $referidos = collect();
            if (in_array($user->rol, ['administrador', 'lider', 'vendedor'])) {
                $referidos = User::where('referido_por', $user->_id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->orderBy('created_at', 'desc')
                    ->take(25)
                    ->get()
                    ->map(function($referido) {
                        return [
                            'id' => (string)$referido->_id,
                            'name' => $referido->name ?? '',
                            'apellidos' => $referido->apellidos ?? '',
                            'email' => $referido->email ?? '',
                            'rol' => $referido->rol ?? '',
                            'activo' => $referido->activo ?? false,
                            'created_at' => $referido->created_at ? $referido->created_at->toISOString() : null
                        ];
                    });
            }

            // Combinar todos los pedidos y ordenarlos
            $todosPedidos = $pedidosComoCliente->concat($pedidosComoVendedor)
                ->sortByDesc('created_at')
                ->values();

            $actividad = [
                'pedidos' => $todosPedidos->take(30),
                'usuarios_referidos' => $referidos,
                'resumen' => [
                    'pedidos_como_cliente' => $pedidosComoCliente->count(),
                    'pedidos_como_vendedor' => $pedidosComoVendedor->count(),
                    'total_referidos' => $referidos->count(),
                    'accesos_ultimo_mes' => $this->contarAccesosUltimoMes($user),
                    'promedio_accesos_diarios' => $this->promedioAccesosDiarios($user),
                    'tiempo_sesion_promedio' => $this->tiempoSesionPromedio($user),
                    'ultimo_acceso' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $actividad
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener actividad del perfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar actividad: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * API: Obtener estadísticas en tiempo real
     */
    public function getStatsRealtime()
    {
        $user = auth()->user();

        $pedidosCliente = Pedido::where('user_id', $user->_id)->count();
        $pedidosVendedor = Pedido::where('vendedor_id', $user->_id)->count();
        $totalReferidos = User::where('referido_por', $user->_id)->count();

        $ventasTotal = to_float(Pedido::where('vendedor_id', $user->_id)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final'));

        $comisionesTotal = to_float($user->comisiones()->sum('monto'));

        $stats = [
            'pedidos_cliente' => $pedidosCliente,
            'pedidos_vendedor' => $pedidosVendedor,
            'total_referidos' => $totalReferidos,
            'ventas_total' => $ventasTotal,
            'comisiones_total' => $comisionesTotal,
            'timestamp' => now()->toIso8601String()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * API: Obtener actividad en tiempo real
     */
    public function getActivityRealtime()
    {
        $user = auth()->user();
        $actividad = [];

        // Pedidos recientes
        $pedidosRecientes = Pedido::where(function($query) use ($user) {
            $query->where('user_id', $user->_id)
                  ->orWhere('vendedor_id', $user->_id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        foreach ($pedidosRecientes as $pedido) {
            $tipo = $pedido->user_id == $user->_id ? 'cliente' : 'vendedor';
            $actividad[] = [
                'id' => $pedido->_id,
                'tipo' => 'pedido',
                'descripcion' => 'Pedido ' . ($pedido->numero_pedido ?? '#'.substr($pedido->_id, -6)) . ' como ' . $tipo,
                'monto' => $pedido->total_final,
                'tiempo' => $pedido->created_at->diffForHumans()
            ];
        }

        // Referidos recientes
        if (in_array($user->rol, ['administrador', 'lider', 'vendedor'])) {
            $referidos = User::where('referido_por', $user->_id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($referidos as $referido) {
                $actividad[] = [
                    'id' => $referido->_id,
                    'tipo' => 'usuario',
                    'descripcion' => 'Nuevo referido: ' . $referido->name,
                    'tiempo' => $referido->created_at->diffForHumans()
                ];
            }
        }

        // Ordenar por más reciente
        usort($actividad, function($a, $b) {
            return strcmp($b['tiempo'], $a['tiempo']);
        });

        return response()->json([
            'success' => true,
            'actividad' => array_slice($actividad, 0, 15)
        ]);
    }

    /**
     * API: Obtener nuevas notificaciones
     */
    public function getNotificacionesNuevas()
    {
        $user = auth()->user();
        $notificaciones = [];

        // Verificar nuevos pedidos
        $pedidosNuevos = Pedido::where(function($query) use ($user) {
            $query->where('user_id', $user->_id)
                  ->orWhere('vendedor_id', $user->_id);
        })
        ->where('created_at', '>=', now()->subMinutes(15))
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();

        foreach ($pedidosNuevos as $pedido) {
            $notificaciones[] = [
                'id' => $pedido->_id,
                'tipo' => 'success',
                'mensaje' => 'Nuevo pedido #' . ($pedido->numero_pedido ?? substr($pedido->_id, -6)) . ' - $' . number_format($pedido->total_final, 0),
                'timestamp' => $pedido->created_at->diffForHumans()
            ];
        }

        // Verificar nuevos referidos
        if (in_array($user->rol, ['administrador', 'lider', 'vendedor'])) {
            $referidosNuevos = User::where('referido_por', $user->_id)
                ->where('created_at', '>=', now()->subMinutes(15))
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($referidosNuevos as $referido) {
                $notificaciones[] = [
                    'id' => $referido->_id,
                    'tipo' => 'info',
                    'mensaje' => 'Nuevo referido: ' . $referido->name . ' se ha unido',
                    'timestamp' => $referido->created_at->diffForHumans()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones,
            'count' => count($notificaciones)
        ]);
    }
}