<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Comision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReferidoController extends Controller
{
    public function index()
    {
        $vendedor = Auth::user();

        // Obtener referidos directos
        $referidos = User::where('referido_por', $vendedor->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        // Calcular conteos y sumas manualmente para cada referido
        foreach ($referidos as $referido) {
            // Contar pedidos del referido como vendedor
            $referido->pedidos_vendedor_count = Pedido::where('vendedor_id', $referido->id)->count();
            
            // Contar sus referidos
            $referido->referidos_count = User::where('referido_por', $referido->id)->count();
            
            // Sumar total de ventas
            $referido->total_ventas = Pedido::where('vendedor_id', $referido->id)->sum('total_final') ?? 0;
        }

        // Estadísticas de la red
        $stats = $this->calcularEstadisticasRed($vendedor);

        // Top referidos por ventas
        $allReferidos = User::where('referido_por', $vendedor->id)->get();
        
        foreach ($allReferidos as $ref) {
            $ref->total_ventas = Pedido::where('vendedor_id', $ref->id)->sum('total_final') ?? 0;
            $ref->pedidos_vendedor_count = Pedido::where('vendedor_id', $ref->id)->count();
        }
        
        $topReferidos = $allReferidos->sortByDesc('total_ventas')->take(5);

        return view('vendedor.referidos.index', compact('referidos', 'stats', 'topReferidos'));
    }

    public function show($id)
    {
        $vendedor = Auth::user();
        $referido = User::where('referido_por', $vendedor->id)
                       ->where('id', $id)
                       ->firstOrFail();

        // Calcular conteos manualmente
        $referido->pedidos_vendedor_count = Pedido::where('vendedor_id', $referido->id)->count();
        $referido->referidos_count = User::where('referido_por', $referido->id)->count();
        $referido->total_ventas = Pedido::where('vendedor_id', $referido->id)->sum('total_final') ?? 0;

        // Ventas del referido por mes (últimos 6 meses)
        $ventasPorMes = $this->obtenerVentasPorMes($referido);

        // Comisiones generadas por este referido
        $comisionesGeneradas = Comision::where('user_id', $vendedor->id)
                                     ->where('referido_id', $referido->id)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(10);

        // Sus propios referidos (segundo nivel)
        $subReferidos = User::where('referido_por', $referido->id)
                           ->limit(10)
                           ->get();
        
        // Calcular conteos para sub-referidos
        foreach ($subReferidos as $subRef) {
            $subRef->pedidos_vendedor_count = Pedido::where('vendedor_id', $subRef->id)->count();
            $subRef->total_ventas = Pedido::where('vendedor_id', $subRef->id)->sum('total_final') ?? 0;
        }

        return view('vendedor.referidos.show', compact('referido', 'ventasPorMes', 'comisionesGeneradas', 'subReferidos'));
    }

    public function invitar()
    {
        $vendedor = Auth::user();

        return view('vendedor.referidos.invitar', compact('vendedor'));
    }

    public function ganancias(Request $request)
    {
        $vendedor = Auth::user();

        // Comisiones por referidos (without 'with' para MongoDB)
        $query = Comision::where('user_id', $vendedor->id)
                         ->where('tipo_comision', 'referido');

        // Filtros
        if ($request->filled('referido_id')) {
            $query->where('referido_id', $request->referido_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $comisionesReferidos = $query->orderBy('created_at', 'desc')->paginate(15);

        // Buscar mejor referido manualmente
        $referidos = User::where('referido_por', $vendedor->id)->get();
        $mejorReferido = null;
        $maxComisiones = 0;
        
        foreach ($referidos as $ref) {
            $totalComisiones = Comision::where('user_id', $vendedor->id)
                                      ->where('tipo_comision', 'referido')
                                      ->where('referido_id', $ref->id)
                                      ->sum('monto_comision');
            
            if ($totalComisiones > $maxComisiones) {
                $maxComisiones = $totalComisiones;
                $mejorReferido = $ref;
            }
        }

        // Estadísticas de ganancias por referidos
        $statsGanancias = [
            'total_comisiones' => Comision::where('user_id', $vendedor->id)
                                         ->where('tipo_comision', 'referido')
                                         ->sum('monto_comision'),
            'mes_actual' => Comision::where('user_id', $vendedor->id)
                                   ->where('tipo_comision', 'referido')
                                   ->whereMonth('created_at', Carbon::now()->month)
                                   ->sum('monto_comision'),
            'mejor_referido' => $mejorReferido,
            'promedio_mensual' => Comision::where('user_id', $vendedor->id)
                                         ->where('tipo_comision', 'referido')
                                         ->where('created_at', '>=', Carbon::now()->subMonths(6))
                                         ->avg('monto_comision') ?? 0
        ];

        // Lista de referidos para el filtro
        $referidosParaFiltro = User::where('referido_por', $vendedor->id)->get();

        return view('vendedor.referidos.ganancias', compact(
            'comisionesReferidos',
            'statsGanancias',
            'referidosParaFiltro'
        ));
    }

    public function red()
    {
        $vendedor = Auth::user();

        // Estructura de la red (nivel 1 y 2)
        $redCompleta = $this->construirEstructuraRed($vendedor);

        return view('vendedor.referidos.red', compact('redCompleta'));
    }

    private function calcularEstadisticasRed($vendedor)
    {
        $mesActual = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        // Referidos directos (nivel 1)
        $referidosDirectos = User::where('referido_por', $vendedor->id)->count();

        // Referidos indirectos (nivel 2)
        $referidosIndirectos = User::whereIn('referido_por',
            User::where('referido_por', $vendedor->id)->pluck('id')
        )->count();

        // Referidos activos (con ventas este mes)
        $referidosActivos = User::where('referido_por', $vendedor->id)
                               ->whereHas('pedidosVendedor', function($q) use ($mesActual, $finMes) {
                                   $q->whereBetween('created_at', [$mesActual, $finMes]);
                               })
                               ->count();

        // Nuevos referidos este mes
        $nuevosReferidosMes = User::where('referido_por', $vendedor->id)
                                 ->whereBetween('created_at', [$mesActual, $finMes])
                                 ->count();

        // Ventas totales de la red
        $ventasRed = Pedido::whereIn('vendedor_id',
            User::where('referido_por', $vendedor->id)->pluck('id')
        )->sum('total_final');

        // Comisiones generadas por referidos
        $comisionesReferidos = Comision::where('user_id', $vendedor->id)
                                      ->where('tipo_comision', 'referido')
                                      ->sum('monto_comision');

        return [
            'referidos_directos' => $referidosDirectos,
            'referidos_indirectos' => $referidosIndirectos,
            'total_red' => $referidosDirectos + $referidosIndirectos,
            'referidos_activos' => $referidosActivos,
            'nuevos_mes' => $nuevosReferidosMes,
            'ventas_red' => $ventasRed,
            'comisiones_referidos' => $comisionesReferidos,
            'promedio_ventas_referido' => $referidosDirectos > 0 ? $ventasRed / $referidosDirectos : 0
        ];
    }

    private function obtenerVentasPorMes($referido)
    {
        $meses = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);

            $ventas = Pedido::where('vendedor_id', $referido->id)
                           ->whereYear('created_at', $mes->year)
                           ->whereMonth('created_at', $mes->month)
                           ->sum('total_final');

            $meses[] = [
                'mes' => $mes->format('M Y'),
                'ventas' => $ventas
            ];
        }

        return $meses;
    }

    private function construirEstructuraRed($vendedor)
    {
        // Nivel 1: Referidos directos
        $nivel1 = User::where('referido_por', $vendedor->id)->get();
        
        // Calcular conteos manualmente para nivel 1
        foreach ($nivel1 as $ref) {
            $ref->pedidos_vendedor_count = Pedido::where('vendedor_id', $ref->id)->count();
            $ref->referidos_count = User::where('referido_por', $ref->id)->count();
            $ref->total_ventas = Pedido::where('vendedor_id', $ref->id)->sum('total_final') ?? 0;
        }

        $redCompleta = [];

        foreach ($nivel1 as $referido) {
            // Nivel 2: Referidos de cada referido directo
            $nivel2 = User::where('referido_por', $referido->id)->get();
            
            // Calcular conteos para nivel 2
            foreach ($nivel2 as $subRef) {
                $subRef->pedidos_vendedor_count = Pedido::where('vendedor_id', $subRef->id)->count();
                $subRef->total_ventas = Pedido::where('vendedor_id', $subRef->id)->sum('total_final') ?? 0;
            }

            $redCompleta[] = [
                'referido' => $referido,
                'sub_referidos' => $nivel2
            ];
        }

        return $redCompleta;
    }

    public function enviarInvitacion(Request $request)
    {
        $request->validate([
            'emails' => 'required|array',
            'emails.*' => 'email',
            'mensaje_personalizado' => 'nullable|string|max:500'
        ]);

        $vendedor = Auth::user();

        // Aquí implementarías el envío de emails de invitación
        // Por ahora simulamos el envío

        $emailsEnviados = count($request->emails);

        return redirect()->back()
                        ->with('success', "Se enviaron {$emailsEnviados} invitaciones exitosamente.");
    }

    public function generarEnlaceReferido()
    {
        $vendedor = Auth::user();

        $enlace = route('register') . '?ref=' . $vendedor->codigo_referido;

        return response()->json([
            'enlace' => $enlace,
            'codigo' => $vendedor->codigo_referido
        ]);
    }

    public function exportar()
    {
        $vendedor = Auth::user();

        $referidos = User::where('referido_por', $vendedor->id)->get();
        
        // Calcular datos para cada referido
        foreach ($referidos as $ref) {
            $ref->pedidos_vendedor_count = Pedido::where('vendedor_id', $ref->id)->count();
            $ref->referidos_count = User::where('referido_por', $ref->id)->count();
            $ref->total_ventas = Pedido::where('vendedor_id', $ref->id)->sum('total_final') ?? 0;
        }

        // Crear CSV
        $filename = 'referidos_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($referidos) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, [
                'Nombre',
                'Apellidos',
                'Email',
                'Teléfono',
                'Fecha Registro',
                'Estado',
                'Total Ventas',
                'Cantidad Pedidos',
                'Referidos Directos'
            ]);

            // Datos
            foreach ($referidos as $ref) {
                fputcsv($file, [
                    $ref->name,
                    $ref->apellidos ?? '',
                    $ref->email,
                    $ref->telefono ?? '',
                    $ref->created_at->format('d/m/Y H:i'),
                    $ref->activo ? 'Activo' : 'Inactivo',
                    '$' . number_format($ref->total_ventas, 0, ',', '.'),
                    $ref->pedidos_vendedor_count,
                    $ref->referidos_count
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function enviarMensaje(Request $request)
    {
        $request->validate([
            'referido_id' => 'required',
            'asunto' => 'required|string|max:200',
            'mensaje' => 'required|string|max:1000'
        ]);

        $vendedor = Auth::user();
        $referido = User::where('id', $request->referido_id)
                       ->where('referido_por', $vendedor->id)
                       ->first();

        if (!$referido) {
            return response()->json([
                'success' => false,
                'message' => 'Referido no encontrado o no pertenece a tu red'
            ], 404);
        }

        try {
            // Crear notificación para el referido (destinatario)
            $notificacionReferido = \App\Models\Notificacion::create([
                'user_id' => $referido->id,
                'user_data' => [
                    'name' => $referido->name,
                    'email' => $referido->email
                ],
                'titulo' => $request->asunto,
                'mensaje' => $request->mensaje,
                'tipo' => 'mensaje_red',
                'leida' => false,
                'datos_adicionales' => [
                    'remitente_id' => $vendedor->id,
                    'remitente_nombre' => $vendedor->name . ' ' . ($vendedor->apellidos ?? ''),
                    'remitente_rol' => 'vendedor',
                    'destinatario_id' => $referido->id,
                    'destinatario_nombre' => $referido->name . ' ' . ($referido->apellidos ?? ''),
                    'fecha_envio' => now()->toDateTimeString(),
                    'tipo_mensaje' => 'enviado'
                ],
                'canal' => 'sistema'
            ]);

            // Crear notificación para el vendedor (remitente) - Registro de mensaje enviado
            $notificacionVendedor = \App\Models\Notificacion::create([
                'user_id' => $vendedor->id,
                'user_data' => [
                    'name' => $vendedor->name,
                    'email' => $vendedor->email
                ],
                'titulo' => '📤 Mensaje Enviado: ' . $request->asunto,
                'mensaje' => 'Enviaste un mensaje a ' . $referido->name . ' ' . ($referido->apellidos ?? '') . ': "' . $request->mensaje . '"',
                'tipo' => 'mensaje_enviado',
                'leida' => true, // Ya la marcamos como leída porque el vendedor sabe que lo envió
                'datos_adicionales' => [
                    'remitente_id' => $vendedor->id,
                    'remitente_nombre' => $vendedor->name . ' ' . ($vendedor->apellidos ?? ''),
                    'destinatario_id' => $referido->id,
                    'destinatario_nombre' => $referido->name . ' ' . ($referido->apellidos ?? ''),
                    'destinatario_email' => $referido->email,
                    'fecha_envio' => now()->toDateTimeString(),
                    'tipo_mensaje' => 'enviado',
                    'asunto_original' => $request->asunto,
                    'mensaje_original' => $request->mensaje,
                    'notificacion_destinatario_id' => $notificacionReferido->id
                ],
                'canal' => 'sistema'
            ]);

            // Aquí puedes agregar el envío de email si lo deseas
            // Mail::to($referido->email)->send(new MensajeReferido($vendedor, $request->asunto, $request->mensaje));

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado correctamente. El usuario recibirá una notificación.',
                'notificacion_referido_id' => $notificacionReferido->id,
                'notificacion_vendedor_id' => $notificacionVendedor->id,
                'destinatario' => [
                    'nombre' => $referido->name . ' ' . ($referido->apellidos ?? ''),
                    'email' => $referido->email
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al enviar mensaje a referido: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje. Por favor intenta nuevamente.'
            ], 500);
        }
    }

    public function exportarRed()
    {
        $vendedor = Auth::user();
        
        // Construir estructura de la red
        $redCompleta = $this->construirEstructuraRed($vendedor);
        
        // Calcular estadísticas
        $nivel1Count = count($redCompleta);
        $nivel2Count = collect($redCompleta)->sum(function($nodo) { 
            return $nodo['sub_referidos']->count(); 
        });
        $totalRed = $nivel1Count + $nivel2Count;
        $totalVentas = collect($redCompleta)->sum(function($nodo) { 
            return $nodo['referido']->total_ventas ?? 0; 
        });
        
        $data = [
            'vendedor' => $vendedor,
            'redCompleta' => $redCompleta,
            'nivel1Count' => $nivel1Count,
            'nivel2Count' => $nivel2Count,
            'totalRed' => $totalRed,
            'totalVentas' => $totalVentas,
            'fechaExportacion' => now()->format('d/m/Y H:i')
        ];
        
        // Generar PDF
        $pdf = \PDF::loadView('vendedor.referidos.pdf-red', $data);
        
        // Configurar orientación y tamaño
        $pdf->setPaper('A4', 'portrait');
        
        // Nombre del archivo
        $filename = 'red_referidos_' . $vendedor->name . '_' . date('Y-m-d') . '.pdf';
        
        // Descargar
        return $pdf->download($filename);
    }
}