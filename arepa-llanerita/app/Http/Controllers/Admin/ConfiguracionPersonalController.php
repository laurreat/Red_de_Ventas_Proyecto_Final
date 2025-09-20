<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class ConfiguracionPersonalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Configuración personal del usuario
        $configuracion = [
            'interfaz' => [
                'tema' => $user->tema ?? 'light',
                'idioma' => $user->idioma ?? 'es',
                'zona_horaria' => $user->zona_horaria ?? 'America/Bogota',
                'formato_fecha' => $user->formato_fecha ?? 'd/m/Y',
                'formato_hora' => $user->formato_hora ?? 'H:i',
                'sidebar_collapsed' => $user->sidebar_collapsed ?? false,
                'dashboard_widgets' => $user->dashboard_widgets ?? ['stats', 'charts', 'recent'],
            ],
            'notificaciones' => [
                'email_pedidos' => $user->notif_email_pedidos ?? true,
                'email_usuarios' => $user->notif_email_usuarios ?? true,
                'email_sistema' => $user->notif_email_sistema ?? true,
                'email_comisiones' => $user->notif_email_comisiones ?? true,
                'email_reportes' => $user->notif_email_reportes ?? false,
                'sms_urgente' => $user->notif_sms_urgente ?? false,
                'push_browser' => $user->notif_push_browser ?? true,
                'sonido_notificaciones' => $user->sonido_notificaciones ?? true,
                'frecuencia_digest' => $user->frecuencia_digest ?? 'daily',
            ],
            'privacidad' => [
                'perfil_publico' => $user->perfil_publico ?? false,
                'mostrar_email' => $user->mostrar_email ?? false,
                'mostrar_telefono' => $user->mostrar_telefono ?? false,
                'mostrar_ultima_conexion' => $user->mostrar_ultima_conexion ?? true,
                'permitir_mensajes' => $user->permitir_mensajes ?? true,
                'indexar_perfil' => $user->indexar_perfil ?? false,
            ],
            'seguridad' => [
                'sesiones_multiples' => $user->sesiones_multiples ?? true,
                'logout_automatico' => $user->logout_automatico ?? 120, // minutos
                'verificacion_2fa' => $user->verificacion_2fa ?? false,
                'alertas_login' => $user->alertas_login ?? true,
                'historial_actividad' => $user->historial_actividad ?? true,
            ],
            'dashboard' => [
                'widgets_activos' => json_decode($user->widgets_activos ?? '["ventas", "usuarios", "pedidos"]', true),
                'layout_dashboard' => $user->layout_dashboard ?? 'grid',
                'refresh_automatico' => $user->refresh_automatico ?? 30, // segundos
                'mostrar_tips' => $user->mostrar_tips ?? true,
                'densidade_informacion' => $user->densidade_informacion ?? 'normal',
            ]
        ];

        // Widgets disponibles para el dashboard
        $widgetsDisponibles = [
            'ventas' => 'Resumen de Ventas',
            'usuarios' => 'Usuarios Recientes',
            'pedidos' => 'Pedidos Pendientes',
            'comisiones' => 'Mis Comisiones',
            'actividad' => 'Actividad del Sistema',
            'calendario' => 'Calendario',
            'notificaciones' => 'Centro de Notificaciones',
            'accesos_rapidos' => 'Accesos Rápidos',
            'graficos_ventas' => 'Gráficos de Ventas',
            'ranking_vendedores' => 'Ranking de Vendedores'
        ];

        // Temas disponibles
        $temasDisponibles = [
            'light' => 'Claro',
            'dark' => 'Oscuro',
            'auto' => 'Automático (Según sistema)'
        ];

        // Idiomas disponibles
        $idiomasDisponibles = [
            'es' => 'Español',
            'en' => 'English',
            'pt' => 'Português'
        ];

        // Zonas horarias disponibles
        $zonasHorarias = [
            'America/Bogota' => 'Bogotá (COT)',
            'America/Lima' => 'Lima (PET)',
            'America/Caracas' => 'Caracas (VET)',
            'America/Mexico_City' => 'Ciudad de México (CST)',
            'America/Argentina/Buenos_Aires' => 'Buenos Aires (ART)',
            'America/Santiago' => 'Santiago (CLT)',
            'America/Sao_Paulo' => 'São Paulo (BRT)',
            'UTC' => 'UTC (Tiempo Universal)'
        ];

        return view('admin.configuracion-personal.index', compact(
            'configuracion',
            'widgetsDisponibles',
            'temasDisponibles',
            'idiomasDisponibles',
            'zonasHorarias'
        ));
    }

    public function updateInterfaz(Request $request)
    {
        $request->validate([
            'tema' => 'required|in:light,dark,auto',
            'idioma' => 'required|in:es,en,pt',
            'zona_horaria' => 'required|string',
            'formato_fecha' => 'required|in:d/m/Y,m/d/Y,Y-m-d',
            'formato_hora' => 'required|in:H:i,h:i A',
            'sidebar_collapsed' => 'boolean',
            'dashboard_widgets' => 'array'
        ]);

        try {
            $user = auth()->user();

            $user->update([
                'tema' => $request->tema,
                'idioma' => $request->idioma,
                'zona_horaria' => $request->zona_horaria,
                'formato_fecha' => $request->formato_fecha,
                'formato_hora' => $request->formato_hora,
                'sidebar_collapsed' => $request->has('sidebar_collapsed'),
                'dashboard_widgets' => $request->dashboard_widgets ?? []
            ]);

            return redirect()->route('admin.configuracion-personal.index')
                ->with('success', 'Configuración de interfaz actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar configuración: ' . $e->getMessage());
        }
    }

    public function updateNotificaciones(Request $request)
    {
        try {
            $user = auth()->user();

            $user->update([
                'notif_email_pedidos' => $request->has('email_pedidos'),
                'notif_email_usuarios' => $request->has('email_usuarios'),
                'notif_email_sistema' => $request->has('email_sistema'),
                'notif_email_comisiones' => $request->has('email_comisiones'),
                'notif_email_reportes' => $request->has('email_reportes'),
                'notif_sms_urgente' => $request->has('sms_urgente'),
                'notif_push_browser' => $request->has('push_browser'),
                'sonido_notificaciones' => $request->has('sonido_notificaciones'),
                'frecuencia_digest' => $request->frecuencia_digest ?? 'daily'
            ]);

            return redirect()->route('admin.configuracion-personal.index')
                ->with('success', 'Configuración de notificaciones actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar notificaciones: ' . $e->getMessage());
        }
    }

    public function updatePrivacidad(Request $request)
    {
        try {
            $user = auth()->user();

            $user->update([
                'perfil_publico' => $request->has('perfil_publico'),
                'mostrar_email' => $request->has('mostrar_email'),
                'mostrar_telefono' => $request->has('mostrar_telefono'),
                'mostrar_ultima_conexion' => $request->has('mostrar_ultima_conexion'),
                'permitir_mensajes' => $request->has('permitir_mensajes'),
                'indexar_perfil' => $request->has('indexar_perfil')
            ]);

            return redirect()->route('admin.configuracion-personal.index')
                ->with('success', 'Configuración de privacidad actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar privacidad: ' . $e->getMessage());
        }
    }

    public function updateSeguridad(Request $request)
    {
        $request->validate([
            'logout_automatico' => 'required|integer|min:5|max:480',
            'refresh_automatico' => 'required|integer|min:10|max:300'
        ]);

        try {
            $user = auth()->user();

            $user->update([
                'sesiones_multiples' => $request->has('sesiones_multiples'),
                'logout_automatico' => $request->logout_automatico,
                'verificacion_2fa' => $request->has('verificacion_2fa'),
                'alertas_login' => $request->has('alertas_login'),
                'historial_actividad' => $request->has('historial_actividad'),
                'refresh_automatico' => $request->refresh_automatico
            ]);

            return redirect()->route('admin.configuracion-personal.index')
                ->with('success', 'Configuración de seguridad actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar seguridad: ' . $e->getMessage());
        }
    }

    public function updateDashboard(Request $request)
    {
        $request->validate([
            'widgets_activos' => 'array',
            'layout_dashboard' => 'required|in:grid,list,compact',
            'densidade_informacion' => 'required|in:compact,normal,detailed'
        ]);

        try {
            $user = auth()->user();

            $user->update([
                'widgets_activos' => json_encode($request->widgets_activos ?? []),
                'layout_dashboard' => $request->layout_dashboard,
                'densidade_informacion' => $request->densidade_informacion,
                'mostrar_tips' => $request->has('mostrar_tips')
            ]);

            return redirect()->route('admin.configuracion-personal.index')
                ->with('success', 'Configuración del dashboard actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar dashboard: ' . $e->getMessage());
        }
    }

    public function reset()
    {
        try {
            $user = auth()->user();

            // Resetear configuraciones a valores por defecto
            $user->update([
                'tema' => 'light',
                'idioma' => 'es',
                'zona_horaria' => 'America/Bogota',
                'formato_fecha' => 'd/m/Y',
                'formato_hora' => 'H:i',
                'sidebar_collapsed' => false,
                'dashboard_widgets' => ['stats', 'charts', 'recent'],
                'notif_email_pedidos' => true,
                'notif_email_usuarios' => true,
                'notif_email_sistema' => true,
                'notif_email_comisiones' => true,
                'notif_email_reportes' => false,
                'notif_sms_urgente' => false,
                'notif_push_browser' => true,
                'sonido_notificaciones' => true,
                'frecuencia_digest' => 'daily',
                'perfil_publico' => false,
                'mostrar_email' => false,
                'mostrar_telefono' => false,
                'mostrar_ultima_conexion' => true,
                'permitir_mensajes' => true,
                'indexar_perfil' => false,
                'sesiones_multiples' => true,
                'logout_automatico' => 120,
                'verificacion_2fa' => false,
                'alertas_login' => true,
                'historial_actividad' => true,
                'widgets_activos' => json_encode(['ventas', 'usuarios', 'pedidos']),
                'layout_dashboard' => 'grid',
                'refresh_automatico' => 30,
                'mostrar_tips' => true,
                'densidade_informacion' => 'normal'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configuración personal restablecida a valores por defecto'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        try {
            $user = auth()->user();

            $configuracion = [
                'usuario' => [
                    'id' => $user->_id,
                    'nombre' => $user->name,
                    'email' => $user->email,
                    'fecha_exportacion' => now()->toISOString()
                ],
                'configuracion' => [
                    'interfaz' => [
                        'tema' => $user->tema ?? 'light',
                        'idioma' => $user->idioma ?? 'es',
                        'zona_horaria' => $user->zona_horaria ?? 'America/Bogota',
                        'formato_fecha' => $user->formato_fecha ?? 'd/m/Y',
                        'formato_hora' => $user->formato_hora ?? 'H:i',
                        'sidebar_collapsed' => $user->sidebar_collapsed ?? false,
                    ],
                    'notificaciones' => [
                        'email_pedidos' => $user->notif_email_pedidos ?? true,
                        'email_usuarios' => $user->notif_email_usuarios ?? true,
                        'email_sistema' => $user->notif_email_sistema ?? true,
                        'sms_urgente' => $user->notif_sms_urgente ?? false,
                        'push_browser' => $user->notif_push_browser ?? true,
                        'frecuencia_digest' => $user->frecuencia_digest ?? 'daily',
                    ],
                    'dashboard' => [
                        'widgets_activos' => json_decode($user->widgets_activos ?? '[]', true),
                        'layout_dashboard' => $user->layout_dashboard ?? 'grid',
                        'refresh_automatico' => $user->refresh_automatico ?? 30,
                    ]
                ]
            ];

            $json = json_encode($configuracion, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $filename = 'configuracion_personal_' . $user->_id . '_' . now()->format('Y-m-d') . '.json';

            return response($json)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al exportar configuración: ' . $e->getMessage());
        }
    }
}