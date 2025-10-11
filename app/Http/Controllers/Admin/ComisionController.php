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

        // Obtener pedidos en el rango de fechas
        $pedidosQuery = Pedido::whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->whereNotNull('vendedor_id');

        if ($vendedorId) {
            $pedidosQuery->where('vendedor_id', $vendedorId);
        }

        $pedidos = $pedidosQuery->get();

        // Agrupar y calcular comisiones por vendedor
        $comisionesPorVendedor = $pedidos->groupBy('vendedor_id')->map(function ($pedidosVendedor) {
            $vendedorId = $pedidosVendedor->first()->vendedor_id;
            $vendedor = User::find($vendedorId);

            if (!$vendedor) {
                return null;
            }

            $totalPedidos = $pedidosVendedor->count();
            $pedidosEntregados = $pedidosVendedor->where('estado', 'entregado');
            $totalVentas = to_float($pedidosEntregados->sum('total_final'));
            $comisionGanada = $totalVentas * 0.1;

            $pedidosPendientes = $pedidosVendedor->whereNotIn('estado', ['entregado', 'cancelado']);
            $comisionPendiente = to_float($pedidosPendientes->sum('total_final')) * 0.1;

            return (object)[
                'id' => $vendedor->_id,
                'name' => $vendedor->name,
                'email' => $vendedor->email,
                'telefono' => $vendedor->telefono,
                'fecha_registro' => $vendedor->created_at,
                'total_pedidos' => $totalPedidos,
                'pedidos_entregados' => $pedidosEntregados->count(),
                'total_ventas' => $totalVentas,
                'comision_ganada' => $comisionGanada,
                'comision_pendiente' => $comisionPendiente
            ];
        })->filter()->sortByDesc('total_ventas');

        $comisiones = $comisionesPorVendedor;

        // Estadísticas generales
        $stats = [
            'total_comisiones_ganadas' => to_float($comisiones->sum('comision_ganada')),
            'total_comisiones_pendientes' => to_float($comisiones->sum('comision_pendiente')),
            'vendedores_activos' => $comisiones->where('total_pedidos', '>', 0)->count(),
            'promedio_comision' => $comisiones->count() > 0 ? to_float($comisiones->avg('comision_ganada')) : 0,
            'total_ventas' => to_float($comisiones->sum('total_ventas')),
            'mejor_vendedor' => $comisiones->sortByDesc('total_ventas')->first()
        ];

        // Comisiones por día
        $comisionesPorDia = $pedidos->groupBy(function ($pedido) {
            return $pedido->created_at->format('Y-m-d');
        })->map(function ($pedidosDia, $fecha) {
            $pedidosEntregados = $pedidosDia->where('estado', 'entregado');
            $ventas = to_float($pedidosEntregados->sum('total_final'));

            return (object)[
                'fecha' => $fecha,
                'pedidos' => $pedidosDia->count(),
                'ventas' => $ventas,
                'comisiones' => $ventas * 0.1
            ];
        })->sortBy('fecha')->values();

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
            'total_ventas' => to_float($pedidos->where('estado', 'entregado')->sum('total_final')),
            'comision_ganada' => to_float($pedidos->where('estado', 'entregado')->sum('total_final')) * 0.1,
            'comision_pendiente' => to_float($pedidos->whereNotIn('estado', ['entregado', 'cancelado'])->sum('total_final')) * 0.1,
            'tasa_conversion' => $pedidos->count() > 0 ? ($pedidos->where('estado', 'entregado')->count() / $pedidos->count()) * 100 : 0
        ];

        // Comisiones por mes (últimos 6 meses)
        $pedidosUltimosMeses = Pedido::where('vendedor_id', $id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->get();

        $comisionesPorMes = $pedidosUltimosMeses->groupBy(function ($pedido) {
            return $pedido->created_at->format('Y-m');
        })->map(function ($pedidosMes, $periodoMes) {
            $pedidosEntregados = $pedidosMes->where('estado', 'entregado');
            $ventas = to_float($pedidosEntregados->sum('total_final'));

            [$año, $mes] = explode('-', $periodoMes);

            return (object)[
                'año' => (int)$año,
                'mes' => (int)$mes,
                'periodo' => $periodoMes,
                'pedidos' => $pedidosMes->count(),
                'ventas' => $ventas,
                'comision' => $ventas * 0.1
            ];
        })->sortByDesc('periodo')->values();

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
            $totalVentas = to_float($pedidos->sum('total_final'));
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
            'total_comisiones' => to_float($resumenCalculo->sum('comision'))
        ]);
    }

    public function exportar(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $formato = $request->get('formato', 'excel');

        // Obtener datos de comisiones
        $pedidosQuery = Pedido::whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->whereNotNull('vendedor_id');

        $pedidos = $pedidosQuery->get();

        // Agrupar y calcular comisiones por vendedor
        $comisionesPorVendedor = $pedidos->groupBy('vendedor_id')->map(function ($pedidosVendedor) {
            $vendedorId = $pedidosVendedor->first()->vendedor_id;
            $vendedor = User::find($vendedorId);

            if (!$vendedor) {
                return null;
            }

            $totalPedidos = $pedidosVendedor->count();
            $pedidosEntregados = $pedidosVendedor->where('estado', 'entregado');
            $totalVentas = to_float($pedidosEntregados->sum('total_final'));
            $comisionGanada = $totalVentas * 0.1;

            return [
                'Vendedor' => $vendedor->name,
                'Email' => $vendedor->email,
                'Teléfono' => $vendedor->telefono ?? 'N/A',
                'Total Pedidos' => $totalPedidos,
                'Pedidos Entregados' => $pedidosEntregados->count(),
                'Total Ventas' => $totalVentas,
                'Comisión Ganada' => $comisionGanada,
                'Porcentaje Comisión' => '10%'
            ];
        })->filter()->values();

        if ($formato === 'csv') {
            return $this->exportarCSV($comisionesPorVendedor, $fechaInicio, $fechaFin);
        } elseif ($formato === 'pdf') {
            return $this->exportarPDF($comisionesPorVendedor, $fechaInicio, $fechaFin);
        } else {
            return $this->exportarExcel($comisionesPorVendedor, $fechaInicio, $fechaFin);
        }
    }

    private function exportarCSV($datos, $fechaInicio, $fechaFin)
    {
        $filename = "comisiones_{$fechaInicio}_a_{$fechaFin}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($datos) {
            $file = fopen('php://output', 'w');

            // Escribir BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            if ($datos->isNotEmpty()) {
                fputcsv($file, array_keys($datos->first()), ';');

                // Datos
                foreach ($datos as $row) {
                    fputcsv($file, array_values($row), ';');
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportarPDF($datos, $fechaInicio, $fechaFin)
    {
        // Estadísticas adicionales para el PDF
        $totalComisiones = $datos->sum('Comisión Ganada');
        $totalVentas = $datos->sum('Total Ventas');
        $totalPedidos = $datos->sum('Total Pedidos');
        $totalEntregados = $datos->sum('Pedidos Entregados');

        $data = [
            'comisiones' => $datos,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'stats' => [
                'total_comisiones' => $totalComisiones,
                'total_ventas' => $totalVentas,
                'total_pedidos' => $totalPedidos,
                'total_entregados' => $totalEntregados,
                'vendedores_activos' => $datos->count(),
                'promedio_comision' => $datos->count() > 0 ? $totalComisiones / $datos->count() : 0
            ]
        ];

        $pdf = \PDF::loadView('admin.comisiones.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = "reporte_comisiones_{$fechaInicio}_a_{$fechaFin}.pdf";

        return $pdf->download($filename);
    }

    private function exportarExcel($datos, $fechaInicio, $fechaFin)
    {
        // Implementación básica usando Maatwebsite\Excel si está disponible
        // Por ahora devolvemos CSV como fallback
        return $this->exportarCSV($datos, $fechaInicio, $fechaFin);
    }
}
