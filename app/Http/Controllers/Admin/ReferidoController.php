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
        // Sanitizar y validar inputs
        $search = $request->get('search') ? strip_tags(trim($request->get('search'))) : null;
        $cedula = $request->get('cedula') ? preg_replace('/[^0-9]/', '', $request->get('cedula')) : null;
        $nivel = $request->get('nivel') ? strip_tags($request->get('nivel')) : null;
        $tipo = $request->get('tipo') ? strip_tags($request->get('tipo')) : null;
        $usuarioSeleccionado = null;

        // Validar tipo de usuario
        if ($tipo && !in_array($tipo, ['vendedor', 'lider', 'cliente'])) {
            $tipo = null; // Reset si es inválido
        }

        // Si hay búsqueda por cédula, buscar el usuario específico
        if ($cedula) {
            try {
                // Validación en tiempo real: verificar formato de cédula
                if (!preg_match('/^[0-9]{6,12}$/', $cedula)) {
                    session()->flash('warning', "Formato de cédula inválido. Debe contener solo números (6-12 dígitos).");
                    return redirect()->route('admin.referidos.index');
                }

                // Verificar disponibilidad de conexión MongoDB - INCLUIR CLIENTES
                $conexionActiva = User::whereIn('rol', ['vendedor', 'lider', 'cliente'])->count();

                $usuarioSeleccionado = User::where('cedula', $cedula)
                    ->whereIn('rol', ['vendedor', 'lider', 'cliente'])
                    ->first();

                // Obtener estadísticas en tiempo real - INCLUIR CLIENTES
                $estadisticasActuales = [
                    'total_usuarios_sistema' => User::whereIn('rol', ['vendedor', 'lider', 'cliente'])->count(),
                    'usuarios_con_cedula' => User::whereIn('rol', ['vendedor', 'lider', 'cliente'])->whereNotNull('cedula')->count(),
                    'ultimo_registro' => User::whereIn('rol', ['vendedor', 'lider', 'cliente'])->latest('created_at')->first()?->created_at,
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
                    session()->flash('warning', "No se encontró ningún usuario con la cédula: {$cedula}. Total de usuarios en el sistema: {$estadisticasActuales['total_usuarios_sistema']} ({$estadisticasActuales['usuarios_con_cedula']} con cédula).");
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

        // Consulta base de usuarios con referidos - INCLUIR CLIENTES
        $usuariosQuery = User::with(['referidor', 'referidos'])
            ->whereIn('rol', ['vendedor', 'lider', 'cliente'])
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

        // Agregar conteo real de referidos a cada usuario paginado
        $usuarios->getCollection()->transform(function($usuario) {
            // Calcular referidos reales desde la base de datos
            $usuario->referidos_count_real = User::where('referido_por', $usuario->_id)->count();
            return $usuario;
        });

        // Estadísticas generales - Calcular datos reales INCLUYENDO CLIENTES
        $todosUsuarios = User::whereIn('rol', ['vendedor', 'lider', 'cliente'])->get();

        // Contar usuarios con referidos reales
        $usuariosConReferidos = $todosUsuarios->filter(function($usuario) {
            return User::where('referido_por', $usuario->_id)->count() > 0;
        })->count();

        // Calcular promedio real de referidos
        $totalReferidos = 0;
        $usuariosQueRefineron = 0;
        foreach ($todosUsuarios as $usuario) {
            $countReferidos = User::where('referido_por', $usuario->_id)->count();
            if ($countReferidos > 0) {
                $totalReferidos += $countReferidos;
                $usuariosQueRefineron++;
            }
        }
        $promedioReferidos = $usuariosQueRefineron > 0 ? round($totalReferidos / $usuariosQueRefineron, 2) : 0;

        // Encontrar la red más grande (real)
        $usuarioConMasReferidos = null;
        $maxReferidos = 0;
        foreach ($todosUsuarios as $usuario) {
            $countReferidos = User::where('referido_por', $usuario->_id)->count();
            if ($countReferidos > $maxReferidos) {
                $maxReferidos = $countReferidos;
                $usuarioConMasReferidos = $usuario;
            }
        }

        $stats = [
            'total_vendedores' => User::where('rol', 'vendedor')->count(),
            'total_lideres' => User::where('rol', 'lider')->count(),
            'total_clientes' => User::where('rol', 'cliente')->count(),
            'usuarios_con_referidos' => $usuariosConReferidos,
            'usuarios_sin_referidor' => User::whereNull('referido_por')->whereIn('rol', ['vendedor', 'lider', 'cliente'])->count(),
            'promedio_referidos' => $promedioReferidos,
            'red_mas_grande' => $usuarioConMasReferidos ? [
                'usuario' => $usuarioConMasReferidos->name,
                'referidos' => $maxReferidos
            ] : null
        ];

        // Redes más activas (top 10) - Calcular referidos reales INCLUYENDO CLIENTES
        $redesActivas = User::whereIn('rol', ['vendedor', 'lider', 'cliente'])
            ->get()
            ->map(function($usuario) {
                // Contar referidos directos reales
                $referidosDirectos = User::where('referido_por', $usuario->_id)->count();
                $usuario->referidos_reales = $referidosDirectos;
                return $usuario;
            })
            ->filter(function($usuario) {
                return $usuario->referidos_reales > 0;
            })
            ->sortByDesc('referidos_reales')
            ->take(10)
            ->values();

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

        // Contar nodos totales recursivamente para debugging
        $totalNodos = $this->contarNodosTotales($redJerarquica);
        \Log::info('=== RED FINAL PARA VISTA ===', [
            'nodos_raiz' => count($redJerarquica),
            'total_nodos_recursivo' => $totalNodos,
            'primer_nodo' => !empty($redJerarquica) ? [
                'id' => $redJerarquica[0]['id'] ?? 'N/A',
                'name' => $redJerarquica[0]['name'] ?? 'N/A',
                'hijos_count' => count($redJerarquica[0]['hijos'] ?? [])
            ] : 'No hay nodos'
        ]);

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
        ))->with('referidos', $usuarios);
    }

    /**
     * Contar nodos totales recursivamente
     */
    private function contarNodosTotales($nodos, &$contador = 0)
    {
        if (!is_array($nodos)) {
            return $contador;
        }

        foreach ($nodos as $nodo) {
            $contador++;
            if (isset($nodo['hijos']) && is_array($nodo['hijos']) && count($nodo['hijos']) > 0) {
                $this->contarNodosTotales($nodo['hijos'], $contador);
            }
        }

        return $contador;
    }

    public function show($id)
    {
        // Validar que el ID sea un ObjectId válido de MongoDB
        if (!preg_match('/^[a-f\d]{24}$/i', $id)) {
            return redirect()->route('admin.referidos.index')
                ->with('error', 'ID de usuario inválido');
        }

        try {
            $usuario = User::find($id);

            if (!$usuario) {
                return redirect()->route('admin.referidos.index')
                    ->with('error', 'Usuario no encontrado');
            }

            // Validar que el usuario sea vendedor, líder o cliente
            if (!in_array($usuario->rol, ['vendedor', 'lider', 'cliente'])) {
                return redirect()->route('admin.referidos.index')
                    ->with('warning', 'Solo se pueden ver redes de vendedores, líderes y clientes');
            }
        } catch (\Exception $e) {
            \Log::error('Error al obtener usuario para red:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.referidos.index')
                ->with('error', 'Error al cargar los datos del usuario');
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
            // Si no se especifica ID, mostrar desde la raíz - INCLUIR CLIENTES
            if (!$id) {
                $usuarios_raiz = User::whereNull('referido_por')
                    ->whereIn('rol', ['vendedor', 'lider', 'cliente'])
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


    private function construirRedJerarquica()
    {
        // ESTRATEGIA MEJORADA: Buscar usuarios raíz INCLUYENDO ADMINISTRADOR
        // Primero intentar encontrar usuarios raíz sin referidor
        $usuarios_raiz = User::whereNull('referido_por')
            ->whereIn('rol', ['vendedor', 'lider', 'cliente', 'administrador'])
            ->get();

        \Log::info('=== CONSTRUYENDO RED JERÁRQUICA COMPLETA ===', [
            'usuarios_raiz_count' => $usuarios_raiz->count(),
            'usuarios_raiz' => $usuarios_raiz->map(function($u) {
                return ['id' => $u->_id, 'name' => $u->name, 'rol' => $u->rol];
            })->toArray()
        ]);

        // Si hay pocos usuarios raíz con referidos, buscar los principales referidores
        $usuariosRaizConReferidos = $usuarios_raiz->filter(function($u) {
            return User::where('referido_por', $u->_id)->count() > 0;
        });

        // Si no hay usuarios raíz con referidos, encontrar los top referidores
        if ($usuariosRaizConReferidos->count() === 0) {
            \Log::info('No hay usuarios raíz con referidos, buscando top referidores...');
            
            // Obtener usuarios con más referidos directos
            $topReferidores = User::whereIn('rol', ['vendedor', 'lider', 'administrador'])
                ->get()
                ->map(function($usuario) {
                    $usuario->referidos_count = User::where('referido_por', $usuario->_id)->count();
                    return $usuario;
                })
                ->filter(function($usuario) {
                    return $usuario->referidos_count > 0;
                })
                ->sortByDesc('referidos_count')
                ->take(10);

            if ($topReferidores->count() > 0) {
                $usuarios_raiz = $topReferidores;
                \Log::info('Usando top referidores como raíz:', [
                    'count' => $usuarios_raiz->count(),
                    'referidores' => $usuarios_raiz->map(function($u) {
                        return [
                            'id' => $u->_id, 
                            'name' => $u->name, 
                            'rol' => $u->rol,
                            'referidos' => $u->referidos_count
                        ];
                    })->toArray()
                ]);
            }
        } else {
            $usuarios_raiz = $usuariosRaizConReferidos;
        }

        // Si aún no hay usuarios, tomar cualquier usuario para mostrar algo
        if ($usuarios_raiz->count() === 0) {
            $usuarios_raiz = User::whereIn('rol', ['vendedor', 'lider', 'cliente'])
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

        $resultado = $this->formatearJerarquia($usuarios_raiz);

        \Log::info('=== RED JERÁRQUICA CONSTRUIDA ===', [
            'nodos_raiz' => count($resultado),
            'estructura_completa' => $resultado
        ]);

        return $resultado;
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
            if ($usuario->rol === 'vendedor' || $usuario->rol === 'cliente') {
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

        // 1. Obtener el líder/vendedor directo (referidor)
        $referidorDirecto = null;
        if ($vendedor->referido_por) {
            \Log::info('Buscando referidor directo...', ['referido_por_id' => $vendedor->referido_por]);
            $referidorDirecto = User::find($vendedor->referido_por);
            if ($referidorDirecto) {
                $todosLosUsuarios->push($referidorDirecto);
                \Log::info('✅ Referidor directo encontrado:', [
                    'referidor' => $referidorDirecto->name,
                    'cedula' => $referidorDirecto->cedula,
                    'rol' => $referidorDirecto->rol
                ]);
            } else {
                \Log::warning('❌ No se encontró el referidor directo');
            }
        } else {
            \Log::info('ℹ️ Vendedor no tiene referidor (es raíz)');
        }

        // 2. Obtener hermanos (otros referidos del mismo referidor)
        if ($referidorDirecto) {
            \Log::info('Buscando hermanos del vendedor...');
            $hermanos = User::where('referido_por', $referidorDirecto->_id)
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

        // 4. Si hay referidor, también incluir algunos de sus otros referidos para contexto
        if ($referidorDirecto) {
            $otrosReferidosReferidor = User::where('referido_por', $referidorDirecto->_id)
                ->where('_id', '!=', $vendedor->_id)
                ->with(['referidos'])
                ->get();

            foreach ($otrosReferidosReferidor as $otroReferido) {
                if (!$todosLosUsuarios->contains('_id', $otroReferido->_id)) {
                    $todosLosUsuarios->push($otroReferido);
                }
                // Incluir también algunos de sus referidos
                $subReferidos = $this->obtenerTodosLosDescendentes($otroReferido);
                foreach ($subReferidos as $subRef) {
                    if (!$todosLosUsuarios->contains('_id', $subRef->_id)) {
                        $todosLosUsuarios->push($subRef);
                    }
                }
            }
        }

        // Quitar duplicados usando unique con _id como string para MongoDB
        $todosLosUsuarios = $todosLosUsuarios->unique(function($user) {
            return (string) $user->_id;
        });

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

        // 5. Usar al referidor como raíz, o al vendedor si no tiene referidor
        $usuarioRaiz = $referidorDirecto ?? $vendedor;

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

        // 4. Combinar todos los usuarios (sin duplicados) usando conversión a string
        $todosLosUsuarios = $ascendentes
            ->merge($descendentes)
            ->merge($hermanos)
            ->unique(function($user) {
                return (string) $user->_id;
            });

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

            // Calcular total de ventas del usuario
            // IMPORTANTE: Los clientes NO generan ventas, solo compran
            $totalVentas = 0;
            if ($usuario->rol !== 'cliente') {
                $totalVentas = \App\Models\Pedido::where('vendedor_id', $usuario->_id)
                    ->whereIn('estado', ['confirmado', 'en_preparacion', 'enviado', 'entregado'])
                    ->sum('total_final');
            }

            $nodoData = [
                'id' => (string) $usuario->_id, // Convertir a string para asegurar unicidad
                'name' => $usuario->name,
                'email' => $usuario->email,
                'cedula' => $usuario->cedula ?? 'N/A',
                'tipo' => $usuario->rol,
                'nivel' => $nivel + 1,
                'referidos_count' => $referidos->count(),
                'total_ventas' => to_float($totalVentas), // Agregar total de ventas usando helper
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
        \Log::info("Formateando jerarquía - Nivel {$nivel}", [
            'usuarios_count' => $usuarios->count(),
            'usuarios' => $usuarios->map(function($u) {
                return ['id' => $u->_id, 'name' => $u->name, 'rol' => $u->rol];
            })->toArray()
        ]);

        return $usuarios->map(function ($usuario) use ($nivel) {
            // Obtener referidos directos
            $referidos = User::where('referido_por', $usuario->_id)->get();

            \Log::info("Procesando usuario '{$usuario->name}' - Nivel {$nivel}", [
                'usuario_id' => $usuario->_id,
                'referidos_count' => $referidos->count()
            ]);

            // Calcular total de ventas del usuario
            // IMPORTANTE: Los clientes NO generan ventas, solo compran
            $totalVentas = 0;
            if ($usuario->rol !== 'cliente') {
                $totalVentas = \App\Models\Pedido::where('vendedor_id', $usuario->_id)
                    ->whereIn('estado', ['confirmado', 'en_preparacion', 'enviado', 'entregado'])
                    ->sum('total_final');
            }

            return [
                'id' => (string) $usuario->_id, // Convertir a string para unicidad en D3.js
                'name' => $usuario->name,
                'email' => $usuario->email,
                'tipo' => $usuario->rol,
                'nivel' => $nivel,
                'referidos_count' => $referidos->count(),
                'total_ventas' => to_float($totalVentas), // Agregar total de ventas usando helper
                'hijos' => $referidos->count() > 0 ? array_values($this->formatearJerarquia($referidos, $nivel + 1)) : []
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
            
            // Convertir Decimal128 a float si es necesario
            if ($pedidosReferido instanceof \MongoDB\BSON\Decimal128) {
                $pedidosReferido = (float) $pedidosReferido->__toString();
            }
            
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
        $usuarios = User::whereIn('rol', ['vendedor', 'lider', 'cliente'])
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
        // Calcular top referidores con datos reales - INCLUIR CLIENTES
        return User::whereIn('rol', ['vendedor', 'lider', 'cliente'])
            ->get()
            ->map(function($usuario) {
                $referidosDirectos = User::where('referido_por', $usuario->_id)->count();
                $usuario->referidos_reales = $referidosDirectos;
                return $usuario;
            })
            ->filter(function($usuario) {
                return $usuario->referidos_reales > 0;
            })
            ->sortByDesc('referidos_reales')
            ->take(10)
            ->values();
    }

    public function exportar(Request $request)
    {
        $formato = $request->get('formato', 'pdf');
        $cedula = $request->get('cedula');

        // Obtener datos de la red de referidos - INCLUIR CLIENTES
        $usuarios = User::whereIn('rol', ['vendedor', 'lider', 'cliente'])
            ->with(['referidor', 'referidos'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Preparar datos para exportación
        $datosExportacion = $usuarios->map(function ($usuario) {
            return [
                'Nombre' => $usuario->name,
                'Email' => $usuario->email,
                'Cédula' => $usuario->cedula ?? 'N/A',
                'Teléfono' => $usuario->telefono ?? 'N/A',
                'Rol' => ucfirst($usuario->rol),
                'Referidor' => $usuario->referidor ? $usuario->referidor->name : 'Sin referidor',
                'Referidos Directos' => $usuario->referidos->count(),
                'Código Referido' => $usuario->codigo_referido ?? 'N/A',
                'Fecha Registro' => $usuario->created_at->format('d/m/Y H:i'),
                'Estado' => $usuario->activo ? 'Activo' : 'Inactivo'
            ];
        });

        // Estadísticas adicionales - INCLUIR CLIENTES
        $stats = [
            'total_usuarios' => $usuarios->count(),
            'total_vendedores' => $usuarios->where('rol', 'vendedor')->count(),
            'total_lideres' => $usuarios->where('rol', 'lider')->count(),
            'total_clientes' => $usuarios->where('rol', 'cliente')->count(),
            'usuarios_con_referidos' => $usuarios->filter(function ($u) { return $u->referidos->count() > 0; })->count(),
            'usuarios_sin_referidor' => $usuarios->filter(function ($u) { return !$u->referidor; })->count(),
            'fecha_exportacion' => now()->format('d/m/Y H:i:s')
        ];

        if ($formato === 'pdf') {
            return $this->exportarPDF($datosExportacion, $stats, $cedula);
        } else {
            return $this->exportarCSV($datosExportacion, $stats, $cedula);
        }
    }

    private function exportarPDF($datos, $stats, $cedula = null)
    {
        $data = [
            'referidos' => $datos,
            'stats' => $stats,
            'cedula_filtro' => $cedula,
            'titulo' => $cedula ? "Red de Referidos - Usuario {$cedula}" : 'Red de Referidos Completa'
        ];

        $pdf = \PDF::loadView('admin.referidos.pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = $cedula ?
            "red_referidos_{$cedula}_" . now()->format('Y-m-d') . ".pdf" :
            "red_referidos_completa_" . now()->format('Y-m-d') . ".pdf";

        return $pdf->download($filename);
    }

    private function exportarCSV($datos, $stats, $cedula = null)
    {
        $filename = $cedula ?
            "red_referidos_{$cedula}_" . now()->format('Y-m-d') . ".csv" :
            "red_referidos_completa_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($datos, $stats) {
            $file = fopen('php://output', 'w');

            // Escribir BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Escribir estadísticas como comentario
            fputcsv($file, ["# Red de Referidos - Exportado el {$stats['fecha_exportacion']}"], ';');
            fputcsv($file, ["# Total usuarios: {$stats['total_usuarios']}"], ';');
            fputcsv($file, ["# Vendedores: {$stats['total_vendedores']} | Líderes: {$stats['total_lideres']}"], ';');
            fputcsv($file, [], ';'); // Línea vacía

            // Headers de datos
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

    private function calcularConversionReferidos()
    {
        $todosUsuarios = User::whereIn('rol', ['vendedor', 'lider', 'cliente'])->get();
        $total_usuarios = $todosUsuarios->count();

        // Contar usuarios con referidos reales
        $usuarios_con_referidos = $todosUsuarios->filter(function($usuario) {
            return User::where('referido_por', $usuario->_id)->count() > 0;
        })->count();

        return $total_usuarios > 0 ? round(($usuarios_con_referidos / $total_usuarios) * 100, 2) : 0;
    }
}
