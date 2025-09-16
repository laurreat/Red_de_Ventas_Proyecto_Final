<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferidoController extends Controller
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
        $search = $request->get('search');
        $nivel = $request->get('nivel');
        $tipo = $request->get('tipo');

        // Consulta base de usuarios con referidos
        $usuariosQuery = User::with(['referidor', 'referidos'])
            ->whereIn('tipo_usuario', ['vendedor', 'lider'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('codigo_referido', 'like', '%' . $search . '%');
                });
            })
            ->when($tipo, function ($query) use ($tipo) {
                return $query->where('tipo_usuario', $tipo);
            });

        $usuarios = $usuariosQuery->orderBy('created_at', 'desc')->paginate(20);

        // Estadísticas generales
        $stats = [
            'total_vendedores' => User::vendedores()->count(),
            'total_lideres' => User::lideres()->count(),
            'usuarios_con_referidos' => User::whereHas('referidos')->count(),
            'usuarios_sin_referidor' => User::whereNull('referidor_id')->whereIn('tipo_usuario', ['vendedor', 'lider'])->count(),
            'promedio_referidos' => $this->calcularPromedioReferidos(),
            'red_mas_grande' => $this->obtenerRedMasGrande()
        ];

        // Redes más activas (top 10)
        $redesActivas = User::withCount('referidos')
            ->whereIn('tipo_usuario', ['vendedor', 'lider'])
            ->having('referidos_count', '>', 0)
            ->orderByDesc('referidos_count')
            ->take(10)
            ->get();

        // Estructura jerárquica para visualización
        $redJerarquica = $this->construirRedJerarquica();

        return view('admin.referidos.index', compact(
            'usuarios',
            'stats',
            'redesActivas',
            'redJerarquica',
            'search',
            'nivel',
            'tipo'
        ));
    }

    public function show($id)
    {
        $usuario = User::with(['referidor', 'referidos.referidos', 'pedidos'])
            ->findOrFail($id);

        // Obtener toda la red del usuario (hasta 5 niveles)
        $redCompleta = $this->obtenerRedCompleta($usuario, 5);

        // Estadísticas del usuario
        $statsUsuario = [
            'referidos_directos' => $usuario->referidos->count(),
            'referidos_totales' => $this->contarReferidosTotales($usuario),
            'ventas_referidos' => $this->calcularVentasReferidos($usuario),
            'comisiones_referidos' => $this->calcularComisionesReferidos($usuario),
            'nivel_en_red' => $this->calcularNivelEnRed($usuario),
            'fecha_registro' => $usuario->created_at
        ];

        // Actividad reciente de la red
        $actividadReciente = $this->obtenerActividadReciente($usuario);

        return view('admin.referidos.show', compact(
            'usuario',
            'redCompleta',
            'statsUsuario',
            'actividadReciente'
        ));
    }

    public function red($id = null)
    {
        // Si no se especifica ID, mostrar desde la raíz
        if (!$id) {
            $usuarios_raiz = User::whereNull('referidor_id')
                ->whereIn('tipo_usuario', ['vendedor', 'lider'])
                ->with(['referidos' => function ($query) {
                    $query->with('referidos.referidos');
                }])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $this->formatearParaVisualizacion($usuarios_raiz)
            ]);
        }

        $usuario = User::with(['referidos' => function ($query) {
            $query->with('referidos.referidos');
        }])->find($id);

        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado']);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatearParaVisualizacion([$usuario])
        ]);
    }

    public function estadisticas()
    {
        $stats = [
            'distribucion_por_nivel' => $this->obtenerDistribucionPorNivel(),
            'crecimiento_mensual' => $this->obtenerCrecimientoMensual(),
            'top_referidores' => $this->obtenerTopReferidores(),
            'conversion_referidos' => $this->calcularConversionReferidos()
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    // Métodos privados auxiliares

    private function calcularPromedioReferidos()
    {
        $usuarios_con_referidos = User::whereHas('referidos')->count();
        $total_referidos = User::whereNotNull('referidor_id')->count();

        return $usuarios_con_referidos > 0 ? round($total_referidos / $usuarios_con_referidos, 2) : 0;
    }

    private function obtenerRedMasGrande()
    {
        $usuario = User::withCount('referidos')
            ->whereIn('tipo_usuario', ['vendedor', 'lider'])
            ->orderByDesc('referidos_count')
            ->first();

        return $usuario ? [
            'usuario' => $usuario->name,
            'referidos' => $usuario->referidos_count
        ] : null;
    }

    private function construirRedJerarquica()
    {
        $usuarios_raiz = User::whereNull('referidor_id')
            ->whereIn('tipo_usuario', ['vendedor', 'lider'])
            ->with(['referidos' => function ($query) {
                $query->with('referidos.referidos');
            }])
            ->take(10) // Limitar para performance
            ->get();

        return $this->formatearJerarquia($usuarios_raiz);
    }

    private function formatearJerarquia($usuarios, $nivel = 1)
    {
        return $usuarios->map(function ($usuario) use ($nivel) {
            return [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'tipo' => $usuario->tipo_usuario,
                'nivel' => $nivel,
                'referidos_count' => $usuario->referidos->count(),
                'hijos' => $nivel < 3 ? $this->formatearJerarquia($usuario->referidos, $nivel + 1) : []
            ];
        });
    }

    private function obtenerRedCompleta($usuario, $niveles = 5)
    {
        if ($niveles <= 0) return [];

        $red = [];
        foreach ($usuario->referidos as $referido) {
            $red[] = [
                'usuario' => $referido,
                'nivel' => 6 - $niveles,
                'hijos' => $this->obtenerRedCompleta($referido, $niveles - 1)
            ];
        }

        return $red;
    }

    private function contarReferidosTotales($usuario)
    {
        $count = $usuario->referidos->count();
        foreach ($usuario->referidos as $referido) {
            $count += $this->contarReferidosTotales($referido);
        }
        return $count;
    }

    private function calcularVentasReferidos($usuario)
    {
        $ventas = 0;
        foreach ($usuario->referidos as $referido) {
            $ventas += $referido->pedidos()->where('estado', 'entregado')->sum('total_final');
            $ventas += $this->calcularVentasReferidos($referido);
        }
        return $ventas;
    }

    private function calcularComisionesReferidos($usuario)
    {
        return $this->calcularVentasReferidos($usuario) * 0.03; // 3% comisión por referidos
    }

    private function calcularNivelEnRed($usuario)
    {
        $nivel = 1;
        $actual = $usuario;
        while ($actual->referidor) {
            $nivel++;
            $actual = $actual->referidor;
        }
        return $nivel;
    }

    private function obtenerActividadReciente($usuario)
    {
        // Obtener registros recientes de referidos directos
        return $usuario->referidos()
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    private function formatearParaVisualizacion($usuarios)
    {
        return $usuarios->map(function ($usuario) {
            return [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'tipo' => $usuario->tipo_usuario,
                'codigo_referido' => $usuario->codigo_referido,
                'referidos_count' => $usuario->referidos->count(),
                'children' => $this->formatearParaVisualizacion($usuario->referidos)
            ];
        });
    }

    private function obtenerDistribucionPorNivel()
    {
        // Implementar lógica para calcular distribución por niveles
        return [];
    }

    private function obtenerCrecimientoMensual()
    {
        return User::whereIn('tipo_usuario', ['vendedor', 'lider'])
            ->selectRaw('YEAR(created_at) as año, MONTH(created_at) as mes, COUNT(*) as cantidad')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('año', 'mes')
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();
    }

    private function obtenerTopReferidores()
    {
        return User::withCount('referidos')
            ->whereIn('tipo_usuario', ['vendedor', 'lider'])
            ->having('referidos_count', '>', 0)
            ->orderByDesc('referidos_count')
            ->take(10)
            ->get();
    }

    private function calcularConversionReferidos()
    {
        $total_usuarios = User::whereIn('tipo_usuario', ['vendedor', 'lider'])->count();
        $usuarios_con_referidos = User::whereHas('referidos')->count();

        return $total_usuarios > 0 ? round(($usuarios_con_referidos / $total_usuarios) * 100, 2) : 0;
    }
}
