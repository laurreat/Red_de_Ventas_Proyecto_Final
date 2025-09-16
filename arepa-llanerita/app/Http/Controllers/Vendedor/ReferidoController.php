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
                        ->withCount(['pedidosVendedor', 'referidos'])
                        ->withSum(['pedidosVendedor as total_ventas'], 'total_final')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        // Estadísticas de la red
        $stats = $this->calcularEstadisticasRed($vendedor);

        // Top referidos por ventas
        $topReferidos = User::where('referido_por', $vendedor->id)
                           ->withSum(['pedidosVendedor as total_ventas'], 'total_final')
                           ->orderBy('total_ventas', 'desc')
                           ->limit(5)
                           ->get();

        return view('vendedor.referidos.index', compact('referidos', 'stats', 'topReferidos'));
    }

    public function show($id)
    {
        $vendedor = Auth::user();
        $referido = User::where('referido_por', $vendedor->id)
                       ->where('id', $id)
                       ->withCount(['pedidosVendedor', 'referidos'])
                       ->withSum(['pedidosVendedor as total_ventas'], 'total_final')
                       ->firstOrFail();

        // Ventas del referido por mes (últimos 6 meses)
        $ventasPorMes = $this->obtenerVentasPorMes($referido);

        // Comisiones generadas por este referido
        $comisionesGeneradas = Comision::where('user_id', $vendedor->id)
                                     ->where('referido_id', $referido->id)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(10);

        // Sus propios referidos (segundo nivel)
        $subReferidos = User::where('referido_por', $referido->id)
                           ->withCount('pedidosVendedor')
                           ->limit(10)
                           ->get();

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

        // Comisiones por referidos
        $query = Comision::where('user_id', $vendedor->id)
                         ->where('tipo_comision', 'referido')
                         ->with(['referido']);

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

        // Estadísticas de ganancias por referidos
        $statsGanancias = [
            'total_comisiones' => Comision::where('user_id', $vendedor->id)
                                         ->where('tipo_comision', 'referido')
                                         ->sum('monto_comision'),
            'mes_actual' => Comision::where('user_id', $vendedor->id)
                                   ->where('tipo_comision', 'referido')
                                   ->whereMonth('created_at', Carbon::now()->month)
                                   ->sum('monto_comision'),
            'mejor_referido' => User::where('referido_por', $vendedor->id)
                                   ->withSum(['comisionesGeneradas as total_comisiones' => function($q) use ($vendedor) {
                                       $q->where('user_id', $vendedor->id)
                                         ->where('tipo_comision', 'referido');
                                   }], 'monto_comision')
                                   ->orderBy('total_comisiones', 'desc')
                                   ->first(),
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
        $nivel1 = User::where('referido_por', $vendedor->id)
                     ->withCount(['pedidosVendedor', 'referidos'])
                     ->withSum(['pedidosVendedor as total_ventas'], 'total_final')
                     ->get();

        $redCompleta = [];

        foreach ($nivel1 as $referido) {
            // Nivel 2: Referidos de cada referido directo
            $nivel2 = User::where('referido_por', $referido->id)
                         ->withCount('pedidosVendedor')
                         ->withSum(['pedidosVendedor as total_ventas'], 'total_final')
                         ->get();

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

        $referidos = User::where('referido_por', $vendedor->id)
                        ->withCount(['pedidosVendedor', 'referidos'])
                        ->withSum(['pedidosVendedor as total_ventas'], 'total_final')
                        ->get();

        // Aquí implementarías la exportación
        return response()->json($referidos);
    }
}