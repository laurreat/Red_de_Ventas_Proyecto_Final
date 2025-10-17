<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AyudaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:lider');
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

        return view('lider.ayuda.index', compact('categorias', 'preguntasFrecuentes', 'tutoriales', 'contacto'));
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
     * Obtener categorías de ayuda para líderes
     */
    private function getCategorias()
    {
        return [
            [
                'id' => 'inicio',
                'titulo' => 'Primeros Pasos',
                'icono' => 'bi-rocket-takeoff',
                'descripcion' => 'Guías básicas para líderes',
                'articulos' => 6
            ],
            [
                'id' => 'equipo',
                'titulo' => 'Gestión de Equipo',
                'icono' => 'bi-people',
                'descripcion' => 'Administrar tu equipo de vendedores',
                'articulos' => 10
            ],
            [
                'id' => 'ventas',
                'titulo' => 'Ventas y Pedidos',
                'icono' => 'bi-cart-check',
                'descripcion' => 'Seguimiento de ventas del equipo',
                'articulos' => 8
            ],
            [
                'id' => 'comisiones',
                'titulo' => 'Comisiones',
                'icono' => 'bi-currency-dollar',
                'descripcion' => 'Sistema de comisiones y pagos',
                'articulos' => 7
            ],
            [
                'id' => 'metas',
                'titulo' => 'Metas y Objetivos',
                'icono' => 'bi-target',
                'descripcion' => 'Establecer y seguir metas',
                'articulos' => 6
            ],
            [
                'id' => 'referidos',
                'titulo' => 'Red de Referidos',
                'icono' => 'bi-diagram-3',
                'descripcion' => 'Gestionar tu red de referidos',
                'articulos' => 5
            ],
            [
                'id' => 'reportes',
                'titulo' => 'Reportes',
                'icono' => 'bi-graph-up',
                'descripcion' => 'Generar y analizar reportes',
                'articulos' => 7
            ],
            [
                'id' => 'capacitacion',
                'titulo' => 'Capacitación',
                'icono' => 'bi-book',
                'descripcion' => 'Capacitar a tu equipo',
                'articulos' => 4
            ]
        ];
    }

    /**
     * Obtener preguntas frecuentes para líderes
     */
    private function getPreguntasFrecuentes()
    {
        return [
            [
                'pregunta' => '¿Cómo puedo ver el rendimiento de mi equipo?',
                'respuesta' => 'Ve a Rendimiento en el menú lateral. Allí encontrarás estadísticas detalladas de cada miembro de tu equipo, incluyendo ventas, comisiones y metas alcanzadas.',
                'categoria' => 'equipo'
            ],
            [
                'pregunta' => '¿Cómo asigno metas a mi equipo?',
                'respuesta' => 'Desde la sección de Metas y Objetivos puedes crear metas individuales o grupales. Ingresa el monto objetivo, período y asigna a los vendedores correspondientes.',
                'categoria' => 'metas'
            ],
            [
                'pregunta' => '¿Cómo funciona mi sistema de comisiones?',
                'respuesta' => 'Recibes comisiones por las ventas directas y por las ventas de tu equipo. Las comisiones se calculan automáticamente según los porcentajes configurados y puedes verlas en tiempo real.',
                'categoria' => 'comisiones'
            ],
            [
                'pregunta' => '¿Puedo capacitar a mi equipo desde el sistema?',
                'respuesta' => 'Sí, en la sección Capacitación puedes crear módulos de entrenamiento, asignarlos a vendedores y hacer seguimiento de su progreso.',
                'categoria' => 'capacitacion'
            ],
            [
                'pregunta' => '¿Cómo agrego nuevos miembros a mi equipo?',
                'respuesta' => 'Comparte tu código de referido con los nuevos vendedores. Cuando se registren con tu código, automáticamente formarán parte de tu equipo.',
                'categoria' => 'referidos'
            ],
            [
                'pregunta' => '¿Cómo exporto los reportes de mi equipo?',
                'respuesta' => 'Todos los reportes tienen opciones de exportación. Puedes descargarlos en formato PDF, Excel o CSV usando los botones de exportar.',
                'categoria' => 'reportes'
            ],
            [
                'pregunta' => '¿Qué hago si un vendedor no cumple sus metas?',
                'respuesta' => 'Revisa su historial de ventas, identifica áreas de mejora y asigna capacitaciones específicas. También puedes establecer un plan de acción personalizado.',
                'categoria' => 'equipo'
            ],
            [
                'pregunta' => '¿Puedo ver las ventas en tiempo real?',
                'respuesta' => 'Sí, el dashboard se actualiza automáticamente mostrando las ventas más recientes de tu equipo y las estadísticas actualizadas.',
                'categoria' => 'ventas'
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
                'titulo' => 'Introducción para Líderes',
                'duracion' => '6:45',
                'thumbnail' => '/images/tutorials/lider-intro.jpg',
                'url' => '#',
                'vistas' => 850
            ],
            [
                'titulo' => 'Gestión Efectiva de Equipo',
                'duracion' => '12:30',
                'thumbnail' => '/images/tutorials/team-management.jpg',
                'url' => '#',
                'vistas' => 670
            ],
            [
                'titulo' => 'Establecer y Seguir Metas',
                'duracion' => '8:15',
                'thumbnail' => '/images/tutorials/goals.jpg',
                'url' => '#',
                'vistas' => 590
            ],
            [
                'titulo' => 'Maximizar tus Comisiones',
                'duracion' => '10:00',
                'thumbnail' => '/images/tutorials/commissions-leader.jpg',
                'url' => '#',
                'vistas' => 920
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
