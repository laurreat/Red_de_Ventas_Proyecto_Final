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
        $cedula = $request->get('cedula');
        $nivel = $request->get('nivel');
        $tipo = $request->get('tipo');
        $usuarioSeleccionado = null;

        // Si hay búsqueda por cédula, buscar el usuario específico
        if ($cedula) {
            try {
                // Validación en tiempo real: verificar formato de cédula
                if (!preg_match('/^[0-9]{6,12}$/', $cedula)) {
                    session()->flash('warning', "Formato de cédula inválido: {$cedula}. Debe contener solo números (6-12 dígitos).");
                    return redirect()->route('admin.referidos.index');
                }

                // Verificar disponibilidad de conexión MongoDB
                $conexionActiva = User::whereIn('rol', ['vendedor', 'lider'])->count();

                $usuarioSeleccionado = User::where('cedula', $cedula)
                    ->whereIn('rol', ['vendedor', 'lider'])
                    ->first();

                // Obtener estadísticas en tiempo real
                $estadisticasActuales = [
                    'total_usuarios_sistema' => User::whereIn('rol', ['vendedor', 'lider'])->count(),
                    'usuarios_con_cedula' => User::whereIn('rol', ['vendedor', 'lider'])->whereNotNull('cedula')->count(),
                    'ultimo_registro' => User::whereIn('rol', ['vendedor', 'lider'])->latest('created_at')->first()?->created_at,
                    'busqueda_timestamp' => now()
                ];

                // Debug: mostrar información de búsqueda en tiempo real
                \Log::info('Búsqueda por cédula en tiempo real:', [
                    'cedula_buscada' => $cedula,
                    'usuario_encontrado' => $usuarioSeleccionado ? $usuarioSeleccionado->name : 'No encontrado',
                    'estadisticas_actuales' => $estadisticasActuales
                ]);

                // Mensajes contextuales basados en el estado actual
                if (!$usuarioSeleccionado) {
                    // Verificar si existe algún usuario con esa cédula en otros roles
                    $usuarioOtroRol = User::where('cedula', $cedula)->first();

                    if ($usuarioOtroRol) {
                        session()->flash('warning', "Usuario encontrado con cédula {$cedula} pero tiene rol '{$usuarioOtroRol->rol}'. Solo se muestran vendedores y líderes en la red MLM.");
                    } else {
                        session()->flash('warning', "No se encontró ningún usuario con la cédula: {$cedula}. Total de usuarios en el sistema: {$estadisticasActuales['total_usuarios_sistema']} ({$estadisticasActuales['usuarios_con_cedula']} con cédula).");
                    }
                } else {
                    session()->flash('success', "Usuario encontrado: {$usuarioSeleccionado->name} ({$usuarioSeleccionado->rol}). Cédula: {$usuarioSeleccionado->cedula}");
                }

            } catch (\MongoDB\Driver\Exception\ConnectionException $e) {
                \Log::error('Error de conexión MongoDB:', [
                    'cedula' => $cedula,
                    'error' => $e->getMessage(),
                    'timestamp' => now()
                ]);
                session()->flash('error', "Error de conexión a la base de datos. Por favor, intente nuevamente en unos momentos.");
            } catch (\Exception $e) {
                \Log::error('Error inesperado en búsqueda por cédula:', [
                    'cedula' => $cedula,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'timestamp' => now()
                ]);
                session()->flash('error', "Error inesperado al buscar usuario. Error: " . $e->getMessage());
            }
        }

        // Consulta base de usuarios con referidos
        $usuariosQuery = User::with(['referidor', 'referidos'])
            ->whereIn('rol', ['vendedor', 'lider'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('codigo_referido', 'like', '%' . $search . '%')
                      ->orWhere('cedula', 'like', '%' . $search . '%');
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
        if ($usuarioSeleccionado) {
            $redJerarquica = $this->construirRedCentradaEnUsuario($usuarioSeleccionado);

            // Debug: Log para verificar datos
            \Log::info('Red centrada en usuario - DATOS FINALES:', [
                'usuario_seleccionado' => [
                    'name' => $usuarioSeleccionado->name,
                    'cedula' => $usuarioSeleccionado->cedula,
                    'rol' => $usuarioSeleccionado->rol
                ],
                'red_count' => is_array($redJerarquica) ? count($redJerarquica) : (is_object($redJerarquica) && method_exists($redJerarquica, 'count') ? $redJerarquica->count() : 0),
                'red_estructura_completa' => $redJerarquica,
                'tipo_datos' => gettype($redJerarquica)
            ]);

            // Si la red está vacía, informar al usuario
            if (empty($redJerarquica)) {
                session()->flash('info', "El usuario {$usuarioSeleccionado->name} no tiene una red de referidos configurada.");
            }
        } else {
            $redJerarquica = $this->construirRedJerarquica();
        }

        // Asegurar que redJerarquica siempre sea un array para el frontend
        if (is_object($redJerarquica) && method_exists($redJerarquica, 'toArray')) {
            $redJerarquica = $redJerarquica->toArray();
        } elseif (!is_array($redJerarquica)) {
            $redJerarquica = [];
        }

        return view('admin.referidos.index', compact(
            'usuarios',
            'stats',
            'redesActivas',
            'redJerarquica',
            'usuarioSeleccionado',
            'search',
            'cedula',
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
            return [
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
            ];
        }

        return $this->formatearJerarquia($usuarios_raiz);
    }

    private function construirRedCentradaEnUsuario($usuario)
    {
        // Construir la red centrada en un usuario específico con control total
        // Para vendedores: incluir líder y hermanos para contexto completo
        // Para líderes: incluir línea ascendente completa y descendentes

        try {
            \Log::info('Construyendo red centrada en usuario:', [
                'usuario' => $usuario->name,
                'rol' => $usuario->rol,
                'cedula' => $usuario->cedula
            ]);

            // Estrategia diferente según el rol del usuario
            if ($usuario->rol === 'vendedor') {
                return $this->construirRedParaVendedor($usuario);
            } else {
                return $this->construirRedParaLider($usuario);
            }

        } catch (\Exception $e) {
            \Log::error('Error construyendo red centrada en usuario:', [
                'usuario_id' => $usuario->_id ?? 'N/A',
                'usuario_name' => $usuario->name ?? 'N/A',
                'usuario_rol' => $usuario->rol ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    private function construirRedParaVendedor($vendedor)
    {
        // Para vendedores: mostrar su líder directo, hermanos y sus propios referidos
        \Log::info('=== CONSTRUYENDO RED PARA VENDEDOR ===', [
            'vendedor' => $vendedor->name,
            'cedula' => $vendedor->cedula,
            'referido_por' => $vendedor->referido_por
        ]);

        $todosLosUsuarios = collect([$vendedor]);

        // 1. Obtener el líder directo (referidor)
        $liderDirecto = null;
        if ($vendedor->referido_por) {
            \Log::info('Buscando líder directo...', ['referido_por_id' => $vendedor->referido_por]);
            $liderDirecto = User::find($vendedor->referido_por);
            if ($liderDirecto) {
                $todosLosUsuarios->push($liderDirecto);
                \Log::info('✅ Líder directo encontrado:', [
                    'lider' => $liderDirecto->name,
                    'cedula' => $liderDirecto->cedula,
                    'rol' => $liderDirecto->rol
                ]);
            } else {
                \Log::warning('❌ No se encontró el líder directo');
            }
        } else {
            \Log::info('ℹ️ Vendedor no tiene referidor (es raíz)');
        }

        // 2. Obtener hermanos (otros referidos del mismo líder)
        if ($liderDirecto) {
            \Log::info('Buscando hermanos del vendedor...');
            $hermanos = User::where('referido_por', $liderDirecto->_id)
                ->where('_id', '!=', $vendedor->_id)
                ->get();

            \Log::info('✅ Hermanos encontrados:', [
                'cantidad' => $hermanos->count(),
                'hermanos' => $hermanos->map(function($h) {
                    return ['name' => $h->name, 'cedula' => $h->cedula, 'rol' => $h->rol];
                })->toArray()
            ]);

            foreach ($hermanos as $hermano) {
                $todosLosUsuarios->push($hermano);
            }
        }

        // 3. Obtener todos los descendentes del vendedor
        $descendentes = $this->obtenerTodosLosDescendentes($vendedor);
        $todosLosUsuarios = $todosLosUsuarios->merge($descendentes);

        // 4. Si hay líder, también incluir algunos de sus otros referidos para contexto
        if ($liderDirecto) {
            $otrosReferidosLider = User::where('referido_por', $liderDirecto->_id)
                ->where('_id', '!=', $vendedor->_id)
                ->with(['referidos'])
                ->get();

            foreach ($otrosReferidosLider as $otroReferido) {
                if (!$todosLosUsuarios->contains('_id', $otroReferido->_id)) {
                    $todosLosUsuarios->push($otroReferido);
                }
                // Incluir también algunos de sus referidos
                $subReferidos = $this->obtenerTodosLosDescendentes($otroReferido);
                $todosLosUsuarios = $todosLosUsuarios->merge($subReferidos);
            }
        }

        // Quitar duplicados
        $todosLosUsuarios = $todosLosUsuarios->unique('_id');

        \Log::info('📊 RESUMEN USUARIOS RECOPILADOS:', [
            'total_usuarios' => $todosLosUsuarios->count(),
            'usuarios' => $todosLosUsuarios->map(function($u) {
                return [
                    'name' => $u->name,
                    'cedula' => $u->cedula,
                    'rol' => $u->rol,
                    'referido_por' => $u->referido_por
                ];
            })->toArray()
        ]);

        // 5. Usar al líder como raíz, o al vendedor si no tiene líder
        $usuarioRaiz = $liderDirecto ?? $vendedor;

        \Log::info('🎯 USUARIO RAÍZ SELECCIONADO:', [
            'raiz' => $usuarioRaiz->name,
            'cedula' => $usuarioRaiz->cedula,
            'rol' => $usuarioRaiz->rol
        ]);

        $resultado = $this->formatearJerarquiaPersonalizada(collect([$usuarioRaiz]), $todosLosUsuarios, 0);

        \Log::info('🏁 RESULTADO FINAL RED VENDEDOR:', [
            'estructura_generada' => $resultado,
            'cantidad_nodos_raiz' => count($resultado)
        ]);

        return $resultado;
    }

    private function construirRedParaLider($lider)
    {
        // Para líderes: mostrar línea ascendente completa y toda su descendencia
        \Log::info('Construyendo red para líder:', ['lider' => $lider->name]);

        // 1. Obtener la línea ascendente completa
        $ascendentes = $this->obtenerLineaAscendente($lider);

        // 2. Obtener todos los descendentes
        $descendentes = $this->obtenerTodosLosDescendentes($lider);

        // 3. Obtener hermanos del líder (otros referidos de su referidor)
        $hermanos = collect();
        if ($lider->referido_por) {
            $hermanos = User::where('referido_por', $lider->referido_por)
                ->where('_id', '!=', $lider->_id)
                ->get();

            // Incluir también los referidos de los hermanos para contexto completo
            foreach ($hermanos as $hermano) {
                $referidosHermano = $this->obtenerTodosLosDescendentes($hermano);
                $descendentes = $descendentes->merge($referidosHermano);
            }
        }

        // 4. Combinar todos los usuarios (sin duplicados)
        $todosLosUsuarios = $ascendentes
            ->merge($descendentes)
            ->merge($hermanos)
            ->unique('_id');

        // 5. Encontrar el usuario raíz de la línea
        $usuarioRaiz = $ascendentes->whereNull('referido_por')->first();
        if (!$usuarioRaiz) {
            $usuarioRaiz = $ascendentes->first() ?? $lider;
        }

        return $this->formatearJerarquiaPersonalizada(collect([$usuarioRaiz]), $todosLosUsuarios, 0);
    }


    private function obtenerLineaAscendente($usuario)
    {
        $ascendentes = collect([$usuario]);
        $usuarioActual = $usuario;

        // Subir hasta el raíz
        while ($usuarioActual->referido_por) {
            $padre = User::find($usuarioActual->referido_por);
            if ($padre) {
                $ascendentes->prepend($padre);
                $usuarioActual = $padre;
            } else {
                break;
            }
        }

        return $ascendentes;
    }

    private function obtenerTodosLosDescendentes($usuario)
    {
        return $this->obtenerDescendentesRecursivo($usuario, collect());
    }

    private function obtenerDescendentesRecursivo($usuario, $descendentes)
    {
        $hijos = User::where('referido_por', $usuario->_id)->get();

        foreach ($hijos as $hijo) {
            $descendentes->push($hijo);
            $descendentes = $this->obtenerDescendentesRecursivo($hijo, $descendentes);
        }

        return $descendentes;
    }

    private function formatearJerarquiaPersonalizada($usuariosRaiz, $todosLosUsuarios, $nivel = 0)
    {
        \Log::info("🔧 FORMATEANDO JERARQUÍA NIVEL {$nivel}:", [
            'usuarios_raiz_count' => $usuariosRaiz->count(),
            'usuarios_raiz_nombres' => $usuariosRaiz->pluck('name')->toArray(),
            'todos_usuarios_count' => $todosLosUsuarios->count()
        ]);

        return $usuariosRaiz->map(function ($usuario) use ($todosLosUsuarios, $nivel) {
            // Solo incluir referidos que estén en nuestra lista filtrada
            $referidos = $todosLosUsuarios->where('referido_por', $usuario->_id);

            \Log::info("👤 Procesando usuario '{$usuario->name}' (nivel {$nivel}):", [
                'usuario_id' => $usuario->_id,
                'referidos_encontrados' => $referidos->count(),
                'referidos_nombres' => $referidos->pluck('name')->toArray()
            ]);

            $nodoData = [
                'id' => $usuario->_id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'cedula' => $usuario->cedula ?? 'N/A',
                'tipo' => $usuario->rol,
                'nivel' => $nivel + 1,
                'referidos_count' => $referidos->count(),
                'hijos' => []
            ];

            // Procesar hijos recursivamente solo si hay referidos en nuestra lista filtrada
            if ($referidos->count() > 0) {
                $hijosArray = $this->formatearJerarquiaPersonalizada($referidos, $todosLosUsuarios, $nivel + 1);
                // Asegurar que los hijos sean un array indexado numéricamente
                $nodoData['hijos'] = array_values($hijosArray);
            }

            return $nodoData;
        })->values()->toArray(); // usar values() para reindexar el array
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
                'hijos' => $nivel < 3 ? array_values($this->formatearJerarquia($referidos, $nivel + 1)) : []
            ];
        })->values()->toArray();
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
