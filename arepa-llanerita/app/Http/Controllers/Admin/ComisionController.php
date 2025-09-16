<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado. Solo administradores.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Fechas por defecto (este mes)
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $vendedorId = $request->get('vendedor_id');

        // Comisiones calculadas por vendedor
        $comisionesQuery = DB::table('pedidos')
            ->join('users', 'pedidos.vendedor_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.telefono',
                'users.created_at as fecha_registro',
                DB::raw('COUNT(pedidos.id) as total_pedidos'),
                DB::raw('COUNT(CASE WHEN pedidos.estado = "entregado" THEN 1 END) as pedidos_entregados'),
                DB::raw('SUM(CASE WHEN pedidos.estado = "entregado" THEN pedidos.total_final ELSE 0 END) as total_ventas'),
                DB::raw('SUM(CASE WHEN pedidos.estado = "entregado" THEN pedidos.total_final ELSE 0 END) * 0.1 as comision_ganada'),
                DB::raw('SUM(CASE WHEN pedidos.estado != "entregado" AND pedidos.estado != "cancelado" THEN pedidos.total_final ELSE 0 END) * 0.1 as comision_pendiente')
            )
            ->whereDate('pedidos.created_at', '>=', $fechaInicio)
            ->whereDate('pedidos.created_at', '<=', $fechaFin)
            ->whereNotNull('pedidos.vendedor_id')
            ->when($vendedorId, function ($query) use ($vendedorId) {
                return $query->where('users.id', $vendedorId);
            })
            ->groupBy('users.id', 'users.name', 'users.email', 'users.telefono', 'users.created_at')
            ->orderByDesc('total_ventas');

        $comisiones = $comisionesQuery->get();

        // Estadísticas generales
        $stats = [
            'total_comisiones_ganadas' => $comisiones->sum('comision_ganada'),
            'total_comisiones_pendientes' => $comisiones->sum('comision_pendiente'),
            'vendedores_activos' => $comisiones->where('total_pedidos', '>', 0)->count(),
            'promedio_comision' => $comisiones->count() > 0 ? $comisiones->avg('comision_ganada') : 0,
            'total_ventas' => $comisiones->sum('total_ventas'),
            'mejor_vendedor' => $comisiones->sortByDesc('total_ventas')->first()
        ];

        // Comisiones por día
        $comisionesPorDia = DB::table('pedidos')
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('COUNT(*) as pedidos'),
                DB::raw('SUM(CASE WHEN estado = "entregado" THEN total_final ELSE 0 END) as ventas'),
                DB::raw('SUM(CASE WHEN estado = "entregado" THEN total_final ELSE 0 END) * 0.1 as comisiones')
            )
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->whereNotNull('vendedor_id')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get();

        // Top 5 vendedores
        $topVendedores = $comisiones->take(5);

        // Para filtros
        $vendedores = User::vendedores()->orderBy('name')->get();

        return view('admin.comisiones.index', compact(
            'comisiones',
            'stats',
            'comisionesPorDia',
            'topVendedores',
            'vendedores',
            'fechaInicio',
            'fechaFin',
            'vendedorId'
        ));
    }

    public function show($id, Request $request)
    {
        $vendedor = User::vendedores()->findOrFail($id);

        // Fechas por defecto (este mes)
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        // Pedidos del vendedor
        $pedidos = Pedido::with(['cliente', 'detalles.producto'])
            ->where('vendedor_id', $id)
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Estadísticas del vendedor
        $statsVendedor = [
            'total_pedidos' => $pedidos->total(),
            'pedidos_entregados' => $pedidos->where('estado', 'entregado')->count(),
            'total_ventas' => $pedidos->where('estado', 'entregado')->sum('total_final'),
            'comision_ganada' => $pedidos->where('estado', 'entregado')->sum('total_final') * 0.1,
            'comision_pendiente' => $pedidos->whereNotIn('estado', ['entregado', 'cancelado'])->sum('total_final') * 0.1,
            'tasa_conversion' => $pedidos->count() > 0 ? ($pedidos->where('estado', 'entregado')->count() / $pedidos->count()) * 100 : 0
        ];

        // Comisiones por mes (últimos 6 meses)
        $comisionesPorMes = DB::table('pedidos')
            ->select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as pedidos'),
                DB::raw('SUM(CASE WHEN estado = "entregado" THEN total_final ELSE 0 END) as ventas'),
                DB::raw('SUM(CASE WHEN estado = "entregado" THEN total_final ELSE 0 END) * 0.1 as comision')
            )
            ->where('vendedor_id', $id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return view('admin.comisiones.show', compact(
            'vendedor',
            'pedidos',
            'statsVendedor',
            'comisionesPorMes',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function calcular(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        // Recalcular comisiones para el período
        $pedidosEntregados = Pedido::where('estado', 'entregado')
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->whereNotNull('vendedor_id')
            ->with('vendedor')
            ->get();

        $resumenCalculo = $pedidosEntregados->groupBy('vendedor_id')->map(function ($pedidos) {
            $vendedor = $pedidos->first()->vendedor;
            $totalVentas = $pedidos->sum('total_final');
            $comision = $totalVentas * 0.1; // 10%

            return [
                'vendedor' => $vendedor->name,
                'email' => $vendedor->email,
                'pedidos' => $pedidos->count(),
                'total_ventas' => $totalVentas,
                'comision' => $comision
            ];
        });

        return response()->json([
            'success' => true,
            'mensaje' => 'Comisiones calculadas exitosamente',
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'resumen' => $resumenCalculo,
            'total_comisiones' => $resumenCalculo->sum('comision')
        ]);
    }

    public function exportar(Request $request)
    {
        // TODO: Implementar exportación a Excel/PDF
        return response()->json(['message' => 'Funcionalidad de exportación en desarrollo']);
    }
}
