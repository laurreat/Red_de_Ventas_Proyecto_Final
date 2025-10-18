<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AyudaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

        return view('vendedor.ayuda.index', compact('categorias', 'preguntasFrecuentes', 'tutoriales', 'contacto'));
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
     * Obtener categorías de ayuda para vendedor
     */
    private function getCategorias()
    {
        return [
            [
                'id' => 'inicio',
                'titulo' => 'Primeros Pasos',
                'icono' => 'bi-rocket-takeoff',
                'descripcion' => 'Guías básicas para comenzar a vender',
                'articulos' => 6
            ],
            [
                'id' => 'pedidos',
                'titulo' => 'Pedidos y Ventas',
                'icono' => 'bi-cart-check',
                'descripcion' => 'Crear y gestionar tus pedidos',
                'articulos' => 10
            ],
            [
                'id' => 'clientes',
                'titulo' => 'Gestión de Clientes',
                'icono' => 'bi-people',
                'descripcion' => 'Administrar tu cartera de clientes',
                'articulos' => 8
            ],
            [
                'id' => 'comisiones',
                'titulo' => 'Comisiones y Ganancias',
                'icono' => 'bi-cash-coin',
                'descripcion' => 'Cómo funcionan tus comisiones',
                'articulos' => 7
            ],
            [
                'id' => 'referidos',
                'titulo' => 'Red de Referidos',
                'icono' => 'bi-diagram-3',
                'descripcion' => 'Construir y gestionar tu red',
                'articulos' => 9
            ],
            [
                'id' => 'metas',
                'titulo' => 'Metas y Objetivos',
                'icono' => 'bi-bullseye',
                'descripcion' => 'Establecer y alcanzar metas',
                'articulos' => 5
            ],
            [
                'id' => 'productos',
                'titulo' => 'Catálogo de Productos',
                'icono' => 'bi-box-seam',
                'descripcion' => 'Conocer los productos disponibles',
                'articulos' => 12
            ],
            [
                'id' => 'perfil',
                'titulo' => 'Mi Perfil',
                'icono' => 'bi-person-circle',
                'descripcion' => 'Configurar tu cuenta y privacidad',
                'articulos' => 4
            ]
        ];
    }

    /**
     * Obtener preguntas frecuentes para vendedor
     */
    private function getPreguntasFrecuentes()
    {
        return [
            [
                'pregunta' => '¿Cómo creo un nuevo pedido?',
                'respuesta' => 'Ve a Pedidos > Crear Pedido. Selecciona el cliente, agrega productos al carrito y confirma la venta. El sistema calculará automáticamente tu comisión.',
                'categoria' => 'pedidos'
            ],
            [
                'pregunta' => '¿Cómo gano comisiones?',
                'respuesta' => 'Ganas comisiones por cada venta que realices. Además, obtienes comisiones por las ventas de tus referidos según el nivel de la red.',
                'categoria' => 'comisiones'
            ],
            [
                'pregunta' => '¿Cómo invito nuevos vendedores?',
                'respuesta' => 'En Referidos > Invitar encontrarás tu código único. Compártelo con personas interesadas en vender. Cuando se registren con tu código, se unen a tu red.',
                'categoria' => 'referidos'
            ],
            [
                'pregunta' => '¿Cómo agrego un cliente nuevo?',
                'respuesta' => 'Ve a Clientes > Crear Cliente y completa el formulario con sus datos. También puedes crear clientes directamente al hacer un pedido.',
                'categoria' => 'clientes'
            ],
            [
                'pregunta' => '¿Cuándo recibo mis comisiones?',
                'respuesta' => 'Las comisiones se procesan semanalmente. Puedes ver el estado de tus comisiones en la sección de Comisiones y solicitar retiros.',
                'categoria' => 'comisiones'
            ],
            [
                'pregunta' => '¿Cómo establezco mi meta mensual?',
                'respuesta' => 'En Metas puedes establecer tus objetivos de ventas mensuales. El sistema te mostrará tu progreso en tiempo real.',
                'categoria' => 'metas'
            ],
            [
                'pregunta' => '¿Puedo ver el historial de mis ventas?',
                'respuesta' => 'Sí, en Pedidos > Historial puedes ver todas tus ventas anteriores con detalles y filtros por fecha, cliente o estado.',
                'categoria' => 'pedidos'
            ],
            [
                'pregunta' => '¿Cómo actualizo mi perfil?',
                'respuesta' => 'En Mi Perfil > Editar Perfil puedes actualizar tus datos personales, foto y configuración de privacidad.',
                'categoria' => 'perfil'
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
                'titulo' => 'Cómo Hacer tu Primera Venta',
                'duracion' => '6:30',
                'thumbnail' => '/images/tutorials/first-sale.jpg',
                'url' => '#',
                'vistas' => 2340,
                'categoria' => 'inicio'
            ],
            [
                'titulo' => 'Gestionar Clientes Efectivamente',
                'duracion' => '8:45',
                'thumbnail' => '/images/tutorials/clients.jpg',
                'url' => '#',
                'vistas' => 1890,
                'categoria' => 'clientes'
            ],
            [
                'titulo' => 'Entender el Sistema de Comisiones',
                'duracion' => '12:20',
                'thumbnail' => '/images/tutorials/commissions.jpg',
                'url' => '#',
                'vistas' => 1560,
                'categoria' => 'comisiones'
            ],
            [
                'titulo' => 'Construir tu Red de Referidos',
                'duracion' => '15:00',
                'thumbnail' => '/images/tutorials/network.jpg',
                'url' => '#',
                'vistas' => 980,
                'categoria' => 'referidos'
            ],
            [
                'titulo' => 'Alcanzar tus Metas de Ventas',
                'duracion' => '9:30',
                'thumbnail' => '/images/tutorials/goals.jpg',
                'url' => '#',
                'vistas' => 1120,
                'categoria' => 'metas'
            ],
            [
                'titulo' => 'Conocer el Catálogo de Productos',
                'duracion' => '11:15',
                'thumbnail' => '/images/tutorials/catalog.jpg',
                'url' => '#',
                'vistas' => 1450,
                'categoria' => 'productos'
            ]
        ];
    }

    /**
     * Obtener información de contacto
     */
    private function getContactoInfo()
    {
        return [
            'email' => 'soporte.vendedores@arepallanerita.com',
            'telefono' => '+57 123 456 7890',
            'whatsapp' => '+57 123 456 7890',
            'horario' => 'Lunes a Sábado, 8:00 AM - 8:00 PM',
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
