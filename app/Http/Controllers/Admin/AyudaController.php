<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AyudaController extends Controller
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

    /**
     * Mostrar centro de ayuda
     */
    public function index()
    {
        $categorias = $this->getCategorias();
        $preguntasFrecuentes = $this->getPreguntasFrecuentes();
        $tutoriales = $this->getTutoriales();
        $contacto = $this->getContactoInfo();

        return view('admin.ayuda.index', compact('categorias', 'preguntasFrecuentes', 'tutoriales', 'contacto'));
    }

    /**
     * Buscar en ayuda
     */
    public function buscar(Request $request)
    {
        $query = $request->input('q');
        $resultados = $this->buscarContenido($query);

        return response()->json([
            'success' => true,
            'resultados' => $resultados
        ]);
    }

    /**
     * Enviar ticket de soporte
     */
    public function enviarTicket(Request $request)
    {
        $request->validate([
            'asunto' => 'required|string|max:255',
            'categoria' => 'required|string',
            'descripcion' => 'required|string|min:20',
            'prioridad' => 'required|in:baja,media,alta'
        ]);

        // Aquí puedes implementar el envío de email o guardar en DB
        // Por ahora solo retornamos éxito

        return response()->json([
            'success' => true,
            'message' => 'Ticket enviado exitosamente. Te contactaremos pronto.'
        ]);
    }

    /**
     * Obtener categorías de ayuda
     */
    private function getCategorias()
    {
        return [
            [
                'id' => 'inicio',
                'titulo' => 'Primeros Pasos',
                'icono' => 'bi-rocket-takeoff',
                'descripcion' => 'Guías básicas para comenzar a usar el sistema',
                'articulos' => 8
            ],
            [
                'id' => 'usuarios',
                'titulo' => 'Gestión de Usuarios',
                'icono' => 'bi-people',
                'descripcion' => 'Crear, editar y administrar usuarios',
                'articulos' => 12
            ],
            [
                'id' => 'productos',
                'titulo' => 'Productos y Catálogo',
                'icono' => 'bi-boxes',
                'descripcion' => 'Administrar productos e inventario',
                'articulos' => 15
            ],
            [
                'id' => 'pedidos',
                'titulo' => 'Pedidos y Ventas',
                'icono' => 'bi-cart',
                'descripcion' => 'Gestionar pedidos y procesos de venta',
                'articulos' => 10
            ],
            [
                'id' => 'comisiones',
                'titulo' => 'Comisiones y Referidos',
                'icono' => 'bi-cash-coin',
                'descripcion' => 'Sistema de comisiones y red de referidos',
                'articulos' => 7
            ],
            [
                'id' => 'reportes',
                'titulo' => 'Reportes y Estadísticas',
                'icono' => 'bi-graph-up',
                'descripcion' => 'Generar y analizar reportes',
                'articulos' => 9
            ],
            [
                'id' => 'configuracion',
                'titulo' => 'Configuración',
                'icono' => 'bi-gear',
                'descripcion' => 'Personalizar el sistema',
                'articulos' => 6
            ],
            [
                'id' => 'seguridad',
                'titulo' => 'Seguridad',
                'icono' => 'bi-shield-check',
                'descripcion' => 'Roles, permisos y seguridad',
                'articulos' => 5
            ]
        ];
    }

    /**
     * Obtener preguntas frecuentes
     */
    private function getPreguntasFrecuentes()
    {
        return [
            [
                'pregunta' => '¿Cómo creo un nuevo usuario?',
                'respuesta' => 'Ve a Usuarios > Crear Usuario, completa el formulario con los datos requeridos y asigna el rol correspondiente.',
                'categoria' => 'usuarios'
            ],
            [
                'pregunta' => '¿Cómo funciona el sistema de referidos?',
                'respuesta' => 'Cada usuario tiene un código único de referido. Cuando alguien se registra con ese código, se crea una relación y el referidor puede ganar comisiones.',
                'categoria' => 'comisiones'
            ],
            [
                'pregunta' => '¿Cómo gestiono el inventario de productos?',
                'respuesta' => 'En la sección de Productos puedes agregar, editar y eliminar productos. El sistema actualiza automáticamente el inventario con cada venta.',
                'categoria' => 'productos'
            ],
            [
                'pregunta' => '¿Puedo exportar los reportes?',
                'respuesta' => 'Sí, todos los reportes tienen opciones de exportación en formato PDF, Excel y CSV.',
                'categoria' => 'reportes'
            ],
            [
                'pregunta' => '¿Cómo cambio mi contraseña?',
                'respuesta' => 'Ve a tu Perfil > Seguridad y usa la opción "Cambiar Contraseña".',
                'categoria' => 'seguridad'
            ],
            [
                'pregunta' => '¿Qué significan los diferentes estados de pedidos?',
                'respuesta' => 'Pendiente: Recién creado. En Proceso: Siendo preparado. Enviado: En camino al cliente. Entregado: Completado. Cancelado: Anulado.',
                'categoria' => 'pedidos'
            ]
        ];
    }

    /**
     * Obtener tutoriales en video
     */
    private function getTutoriales()
    {
        return [
            [
                'titulo' => 'Introducción al Sistema',
                'duracion' => '5:30',
                'thumbnail' => '/images/tutorials/intro.jpg',
                'url' => '#',
                'vistas' => 1250
            ],
            [
                'titulo' => 'Gestión de Usuarios',
                'duracion' => '8:15',
                'thumbnail' => '/images/tutorials/users.jpg',
                'url' => '#',
                'vistas' => 890
            ],
            [
                'titulo' => 'Crear y Administrar Productos',
                'duracion' => '10:45',
                'thumbnail' => '/images/tutorials/products.jpg',
                'url' => '#',
                'vistas' => 1050
            ],
            [
                'titulo' => 'Sistema de Comisiones',
                'duracion' => '12:20',
                'thumbnail' => '/images/tutorials/commissions.jpg',
                'url' => '#',
                'vistas' => 670
            ]
        ];
    }

    /**
     * Obtener información de contacto
     */
    private function getContactoInfo()
    {
        return [
            'email' => 'soporte@arepallanerita.com',
            'telefono' => '+57 123 456 7890',
            'whatsapp' => '+57 123 456 7890',
            'horario' => 'Lunes a Viernes, 8:00 AM - 6:00 PM',
            'direccion' => 'Calle Principal #123, Ciudad, País'
        ];
    }

    /**
     * Buscar contenido en ayuda
     */
    private function buscarContenido($query)
    {
        $resultados = [];
        $preguntasFrecuentes = $this->getPreguntasFrecuentes();

        foreach ($preguntasFrecuentes as $faq) {
            if (stripos($faq['pregunta'], $query) !== false || stripos($faq['respuesta'], $query) !== false) {
                $resultados[] = [
                    'tipo' => 'FAQ',
                    'titulo' => $faq['pregunta'],
                    'contenido' => $faq['respuesta'],
                    'categoria' => $faq['categoria']
                ];
            }
        }

        return $resultados;
    }
}
