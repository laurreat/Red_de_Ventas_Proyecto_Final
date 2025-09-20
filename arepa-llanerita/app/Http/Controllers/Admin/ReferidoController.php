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
            ->whereIn('rol', ['vendedor', 'lider'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('codigo_referido', 'like', '%' . $search . '%');
                });
            })
            ->when($tipo, function ($query) use ($tipo) {
                return $query->where('rol', $tipo);
            });

        $usuarios = $usuariosQuery->orderBy('created_at', 'desc')->paginate(20);

        // Estadísticas generales
        $stats = [
            'total_vendedores' => User::where('rol', 'vendedor')->count(),
            'total_lideres' => User::where('rol', 'lider')->count(),
            'usuarios_con_referidos' => User::where('total_referidos', '>', 0)->count(),
            'usuarios_sin_referidor' => User::whereNull('referido_por')->whereIn('rol', ['vendedor', 'lider'])->count(),
            'promedio_referidos' => $this->calcularPromedioReferidos(),
            'red_mas_grande' => $this->obtenerRedMasGrande()
        ];

        // Redes más activas (top 10)
        $redesActivas = User::whereIn('rol', ['vendedor', 'lider'])
            ->where('total_referidos', '>', 0)
            ->orderBy('total_referidos', 'desc')
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
        $usuario = User::find($id);

        if (!$usuario) {
            return redirect()->route('admin.referidos.index')
                ->with('error', 'Usuario no encontrado');
        }

        // Obtener toda la red del usuario (hasta 5 niveles)
        $redCompleta = $this->obtenerRedCompleta($usuario, 5);

        // Obtener referidos directos para estadísticas
        $referidosDirectos = User::where('referido_por', $usuario->_id)->get();

        // Estadísticas del usuario
        $statsUsuario = [
            'referidos_directos' => $referidosDirectos->count(),
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
        try {
            // Si no se especifica ID, mostrar desde la raíz
            if (!$id) {
                $usuarios_raiz = User::whereNull('referido_por')
                    ->whereIn('rol', ['vendedor', 'lider'])
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $this->formatearParaVisualizacion($usuarios_raiz)
                ]);
            }

            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json(['success' => false, 'message' => 'Usuario no encontrado']);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatearParaVisualizacion([$usuario])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener red: ' . $e->getMessage()
            ], 500);
        }
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
        $usuarios_con_referidos = User::where('total_referidos', '>', 0)->count();
        $total_referidos = User::whereNotNull('referido_por')->count();

        return $usuarios_con_referidos > 0 ? round($total_referidos / $usuarios_con_referidos, 2) : 0;
    }

    private function obtenerRedMasGrande()
    {
        $usuario = User::whereIn('rol', ['vendedor', 'lider'])
            ->orderBy('total_referidos', 'desc')
            ->first();

        return $usuario ? [
            'usuario' => $usuario->name,
            'referidos' => $usuario->total_referidos ?? 0
        ] : null;
    }

    private function construirRedJerarquica()
    {
        // Buscar usuarios raíz (sin referidor)
        $usuarios_raiz = User::whereNull('referido_por')
            ->whereIn('rol', ['vendedor', 'lider'])
            ->take(10) // Limitar para performance
            ->get();

        // Si no hay usuarios raíz, tomar cualquier usuario para mostrar algo
        if ($usuarios_raiz->count() === 0) {
            $usuarios_raiz = User::whereIn('rol', ['vendedor', 'lider'])
                ->take(5)
                ->get();
        }

        // Si aún no hay usuarios, crear datos de ejemplo
        if ($usuarios_raiz->count() === 0) {
            return collect([
                [
                    'id' => 'demo-1',
                    'name' => 'Usuario Demo 1',
                    'email' => 'demo1@ejemplo.com',
                    'tipo' => 'lider',
                    'nivel' => 1,
                    'referidos_count' => 3,
                    'hijos' => [
                        [
                            'id' => 'demo-2',
                            'name' => 'Usuario Demo 2',
                            'email' => 'demo2@ejemplo.com',
                            'tipo' => 'vendedor',
                            'nivel' => 2,
                            'referidos_count' => 1,
                            'hijos' => []
                        ],
                        [
                            'id' => 'demo-3',
                            'name' => 'Usuario Demo 3',
                            'email' => 'demo3@ejemplo.com',
                            'tipo' => 'vendedor',
                            'nivel' => 2,
                            'referidos_count' => 2,
                            'hijos' => []
                        ]
                    ]
                ]
            ]);
        }

        return $this->formatearJerarquia($usuarios_raiz);
    }

    private function formatearJerarquia($usuarios, $nivel = 1)
    {
        return $usuarios->map(function ($usuario) use ($nivel) {
            // Obtener referidos directos
            $referidos = User::where('referido_por', $usuario->_id)->get();

            return [
                'id' => $usuario->_id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'tipo' => $usuario->rol,
                'nivel' => $nivel,
                'referidos_count' => $referidos->count(),
                'hijos' => $nivel < 3 ? $this->formatearJerarquia($referidos, $nivel + 1) : []
            ];
        });
    }

    private function obtenerRedCompleta($usuario, $niveles = 5)
    {
        if ($niveles <= 0) return [];

        $red = [];
        $referidos = User::where('referido_por', $usuario->_id)->get();

        foreach ($referidos as $referido) {
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
        $referidos = User::where('referido_por', $usuario->_id)->get();
        $count = $referidos->count();

        foreach ($referidos as $referido) {
            $count += $this->contarReferidosTotales($referido);
        }

        return $count;
    }

    private function calcularVentasReferidos($usuario)
    {
        $ventas = 0;
        $referidos = User::where('referido_por', $usuario->_id)->get();

        foreach ($referidos as $referido) {
            // Usar el modelo Pedido en lugar de la relación
            $pedidosReferido = \App\Models\Pedido::where('vendedor_id', $referido->_id)
                ->where('estado', 'entregado')
                ->sum('total_final');
            $ventas += $pedidosReferido ?? 0;
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

        while ($actual->referido_por) {
            $nivel++;
            $actual = User::find($actual->referido_por);
            if (!$actual) {
                break; // Evitar loop infinito si no se encuentra el referidor
            }
        }

        return $nivel;
    }

    private function obtenerActividadReciente($usuario)
    {
        // Obtener registros recientes de referidos directos
        return User::where('referido_por', $usuario->_id)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    private function formatearParaVisualizacion($usuarios)
    {
        return $usuarios->map(function ($usuario) {
            // Obtener referidos directos de este usuario
            $referidos = User::where('referido_por', $usuario->_id)->get();

            return [
                'id' => $usuario->_id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'tipo' => $usuario->rol,
                'codigo_referido' => $usuario->codigo_referido,
                'referidos_count' => $referidos->count(),
                'children' => $this->formatearParaVisualizacion($referidos)
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
        $usuarios = User::whereIn('rol', ['vendedor', 'lider'])
            ->where('created_at', '>=', now()->subYear())
            ->get();

        return $usuarios->groupBy(function ($usuario) {
            return $usuario->created_at->format('Y-m');
        })->map(function ($usuariosMes, $periodo) {
            [$año, $mes] = explode('-', $periodo);
            return (object)[
                'año' => (int)$año,
                'mes' => (int)$mes,
                'cantidad' => $usuariosMes->count()
            ];
        })->sortByDesc(function ($item) {
            return $item->año * 100 + $item->mes;
        })->values();
    }

    private function obtenerTopReferidores()
    {
        return User::whereIn('rol', ['vendedor', 'lider'])
            ->where('total_referidos', '>', 0)
            ->orderBy('total_referidos', 'desc')
            ->take(10)
            ->get();
    }

    private function calcularConversionReferidos()
    {
        $total_usuarios = User::whereIn('rol', ['vendedor', 'lider'])->count();
        $usuarios_con_referidos = User::where('total_referidos', '>', 0)->count();

        return $total_usuarios > 0 ? round(($usuarios_con_referidos / $total_usuarios) * 100, 2) : 0;
    }
}
