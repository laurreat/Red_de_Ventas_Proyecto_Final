<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esLider() && !auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado. Solo líderes.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = auth()->user();

        // Obtener el equipo del líder (referidos directos e indirectos)
        $equipoDirecto = $user->referidos;
        $equipoTotal = $this->obtenerEquipoCompleto($user);

        // Estadísticas generales
        $stats = [
            'equipo_directo' => $equipoDirecto->count(),
            'equipo_total' => count($equipoTotal),
            'ventas_mes_actual' => $this->calcularVentasMesActual($equipoTotal),
            'comisiones_mes' => $this->calcularComisionesMes($user),
            'meta_mensual' => $user->meta_mensual ?? 0,
            'progreso_meta' => $this->calcularProgresoMeta($user),
        ];

        // Ventas del equipo por día (últimos 30 días)
        $ventasPorDia = $this->obtenerVentasPorDia($equipoTotal);

        // Top performers del equipo
        $topPerformers = $this->obtenerTopPerformers($equipoDirecto);

        // Actividad reciente del equipo
        $actividadReciente = $this->obtenerActividadReciente($equipoTotal);

        // Productos más vendidos por el equipo
        $productosMasVendidos = $this->obtenerProductosMasVendidos($equipoTotal);

        // Metas y objetivos
        $metas = $this->obtenerMetas($user);

        // Estadísticas de crecimiento
        $crecimientoEquipo = $this->obtenerCrecimientoEquipo($user);

        return view('lider.dashboard.index', compact(
            'stats',
            'ventasPorDia',
            'topPerformers',
            'actividadReciente',
            'productosMasVendidos',
            'metas',
            'crecimientoEquipo',
            'equipoDirecto'
        ));
    }

    private function obtenerEquipoCompleto($lider, $nivel = 1, $maxNivel = 5)
    {
        if ($nivel > $maxNivel) {
            return [];
        }

        $equipo = [];
        foreach ($lider->referidos as $referido) {
            $equipo[] = $referido;
            if ($nivel < $maxNivel) {
                $equipo = array_merge($equipo, $this->obtenerEquipoCompleto($referido, $nivel + 1, $maxNivel));
            }
        }

        return $equipo;
    }

    private function calcularVentasMesActual($equipo)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $equipoIds = collect($equipo)->pluck('id')->toArray();

        return Pedido::whereIn('vendedor_id', $equipoIds)
            ->where('created_at', '>=', $inicioMes)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final');
    }

    private function calcularComisionesMes($user)
    {
        return $user->comisiones()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('monto');
    }

    private function calcularProgresoMeta($user)
    {
        if (!$user->meta_mensual || $user->meta_mensual == 0) {
            return 0;
        }

        $ventasActuales = $user->ventas_mes_actual ?? 0;
        return min(($ventasActuales / $user->meta_mensual) * 100, 100);
    }

    private function obtenerVentasPorDia($equipo)
    {
        $equipoIds = collect($equipo)->pluck('id')->toArray();
        $fechaInicio = Carbon::now()->subDays(30);

        return Pedido::whereIn('vendedor_id', $equipoIds)
            ->where('created_at', '>=', $fechaInicio)
            ->where('estado', '!=', 'cancelado')
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad, SUM(total_final) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->keyBy('fecha');
    }

    private function obtenerTopPerformers($equipoDirecto)
    {
        $inicioMes = Carbon::now()->startOfMonth();

        return $equipoDirecto->map(function ($miembro) use ($inicioMes) {
            $ventasMes = Pedido::where('vendedor_id', $miembro->id)
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final');

            $pedidosMes = Pedido::where('vendedor_id', $miembro->id)
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->count();

            return [
                'usuario' => $miembro,
                'ventas_mes' => $ventasMes,
                'pedidos_mes' => $pedidosMes,
                'referidos_mes' => $miembro->referidos()
                    ->where('created_at', '>=', $inicioMes)
                    ->count()
            ];
        })
        ->sortByDesc('ventas_mes')
        ->take(5)
        ->values();
    }

    private function obtenerActividadReciente($equipo)
    {
        $equipoIds = collect($equipo)->pluck('id')->toArray();

        // Pedidos recientes
        $pedidosRecientes = Pedido::with('vendedor', 'cliente')
            ->whereIn('vendedor_id', $equipoIds)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($pedido) {
                return [
                    'tipo' => 'pedido',
                    'descripcion' => "Nueva venta por {$pedido->vendedor->name}",
                    'monto' => $pedido->total_final,
                    'fecha' => $pedido->created_at,
                    'usuario' => $pedido->vendedor
                ];
            });

        // Nuevos referidos
        $nuevosReferidos = User::with('referidor')
            ->whereIn('referido_por', $equipoIds)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($usuario) {
                return [
                    'tipo' => 'referido',
                    'descripcion' => "Nuevo referido de {$usuario->referidor->name}",
                    'fecha' => $usuario->created_at,
                    'usuario' => $usuario
                ];
            });

        return $pedidosRecientes->concat($nuevosReferidos)
            ->sortByDesc('fecha')
            ->take(15)
            ->values();
    }

    private function obtenerProductosMasVendidos($equipo)
    {
        $equipoIds = collect($equipo)->pluck('id')->toArray();
        $inicioMes = Carbon::now()->startOfMonth();

        return DB::table('pedido_detalles')
            ->join('pedidos', 'pedido_detalles.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->whereIn('pedidos.vendedor_id', $equipoIds)
            ->where('pedidos.created_at', '>=', $inicioMes)
            ->where('pedidos.estado', '!=', 'cancelado')
            ->selectRaw('
                productos.nombre,
                productos.precio,
                SUM(pedido_detalles.cantidad) as cantidad_vendida,
                SUM(pedido_detalles.total) as total_ventas
            ')
            ->groupBy('productos.id', 'productos.nombre', 'productos.precio')
            ->orderByDesc('cantidad_vendida')
            ->take(10)
            ->get();
    }

    private function obtenerMetas($user)
    {
        return [
            'ventas_mes' => [
                'objetivo' => $user->meta_mensual ?? 0,
                'actual' => $user->ventas_mes_actual ?? 0,
                'progreso' => $this->calcularProgresoMeta($user)
            ],
            'equipo_mes' => [
                'objetivo' => 10, // Meta de crecimiento del equipo
                'actual' => $user->referidos()->whereMonth('created_at', Carbon::now()->month)->count(),
                'progreso' => min(($user->referidos()->whereMonth('created_at', Carbon::now()->month)->count() / 10) * 100, 100)
            ],
            'comisiones_mes' => [
                'objetivo' => ($user->meta_mensual ?? 0) * 0.15, // 15% de comisión esperada
                'actual' => $this->calcularComisionesMes($user),
                'progreso' => ($user->meta_mensual ?? 0) > 0 ?
                    min(($this->calcularComisionesMes($user) / (($user->meta_mensual ?? 0) * 0.15)) * 100, 100) : 0
            ]
        ];
    }

    private function obtenerCrecimientoEquipo($user)
    {
        $mesesAtras = 6;
        $fechaInicio = Carbon::now()->subMonths($mesesAtras)->startOfMonth();

        return collect(range(0, $mesesAtras - 1))->map(function ($i) use ($user) {
            $fecha = Carbon::now()->subMonths($i);
            $nuevos = $user->referidos()
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->count();

            return [
                'mes' => $fecha->format('M Y'),
                'nuevos_miembros' => $nuevos
            ];
        })->reverse()->values();
    }
}