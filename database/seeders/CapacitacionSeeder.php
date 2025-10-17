<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Capacitacion;
use Carbon\Carbon;

class CapacitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer líder disponible
        $lider = \App\Models\User::where('rol', 'lider')->first();

        if (!$lider) {
            $this->command->warn('⚠ No se encontró ningún usuario con rol "lider". Saltando seeder de capacitaciones.');
            return;
        }

        $liderId = $lider->_id;

        $capacitaciones = [
            [
                'titulo' => 'Técnicas de Venta Efectivas',
                'descripcion' => 'Aprende las técnicas fundamentales para cerrar más ventas y mejorar tus habilidades de negociación.',
                'contenido' => 'En este módulo aprenderás: 1) Identificación de necesidades del cliente, 2) Presentación efectiva de productos, 3) Manejo de objeciones, 4) Técnicas de cierre de ventas, 5) Seguimiento post-venta.',
                'duracion' => '3 horas',
                'nivel' => 'Básico',
                'categoria' => 'Ventas',
                'icono' => 'fa-handshake',
                'objetivos' => [
                    'Dominar las 5 etapas del proceso de venta',
                    'Identificar señales de compra del cliente',
                    'Aplicar técnicas de cierre probadas',
                    'Incrementar la tasa de conversión en un 30%'
                ],
                'recursos' => [
                    'Manual de técnicas de venta PDF',
                    'Plantillas de guiones de ventas',
                    'Videos de casos de éxito',
                    'Ejercicios prácticos interactivos'
                ],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'imagen_url' => '/images/capacitaciones/ventas-efectivas.jpg',
                'orden' => 1,
                'activo' => true,
                'lider_id' => $liderId,
                'asignaciones' => [],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'titulo' => 'Atención al Cliente Excelente',
                'descripcion' => 'Desarrolla habilidades para brindar un servicio al cliente excepcional que genere lealtad.',
                'contenido' => 'Contenido del módulo: 1) Principios de servicio al cliente, 2) Comunicación efectiva, 3) Resolución de conflictos, 4) Gestión de clientes difíciles, 5) Construcción de relaciones a largo plazo.',
                'duracion' => '2.5 horas',
                'nivel' => 'Intermedio',
                'categoria' => 'Servicio al Cliente',
                'icono' => 'fa-users',
                'objetivos' => [
                    'Mejorar la satisfacción del cliente en un 40%',
                    'Reducir quejas y reclamos',
                    'Desarrollar empatía y escucha activa',
                    'Crear experiencias memorables para el cliente'
                ],
                'recursos' => [
                    'Guía de comunicación efectiva',
                    'Casos de estudio reales',
                    'Checklist de calidad de servicio',
                    'Scripts para situaciones difíciles'
                ],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'imagen_url' => '/images/capacitaciones/atencion-cliente.jpg',
                'orden' => 2,
                'activo' => true,
                'lider_id' => $liderId,
                'asignaciones' => [],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'titulo' => 'Conocimiento del Producto',
                'descripcion' => 'Domina las características, beneficios y ventajas competitivas de nuestros productos.',
                'contenido' => 'Aprenderás sobre: 1) Catálogo completo de productos, 2) Características técnicas, 3) Beneficios para el cliente, 4) Comparativas con la competencia, 5) Casos de uso y aplicaciones.',
                'duracion' => '4 horas',
                'nivel' => 'Básico',
                'categoria' => 'Producto',
                'icono' => 'fa-box-open',
                'objetivos' => [
                    'Conocer el 100% del catálogo de productos',
                    'Identificar el producto adecuado para cada cliente',
                    'Comunicar valor de forma efectiva',
                    'Responder preguntas técnicas con confianza'
                ],
                'recursos' => [
                    'Catálogo digital interactivo',
                    'Fichas técnicas de productos',
                    'Videos demostrativos',
                    'Cuestionarios de evaluación'
                ],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'imagen_url' => '/images/capacitaciones/conocimiento-producto.jpg',
                'orden' => 3,
                'activo' => true,
                'lider_id' => $liderId,
                'asignaciones' => [],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'titulo' => 'Estrategias de Prospección',
                'descripcion' => 'Aprende a identificar y captar nuevos clientes potenciales de manera efectiva.',
                'contenido' => 'Módulo incluye: 1) Definición de perfil de cliente ideal, 2) Fuentes de prospección, 3) Técnicas de acercamiento, 4) Cualificación de prospectos, 5) Construcción de pipeline de ventas.',
                'duracion' => '3.5 horas',
                'nivel' => 'Intermedio',
                'categoria' => 'Ventas',
                'icono' => 'fa-search',
                'objetivos' => [
                    'Generar 50+ prospectos calificados al mes',
                    'Mejorar tasa de conversión de prospecto a cliente',
                    'Optimizar tiempo de prospección',
                    'Utilizar herramientas digitales para prospectar'
                ],
                'recursos' => [
                    'Plantilla de perfil de cliente ideal',
                    'Lista de fuentes de prospección',
                    'Scripts de contacto inicial',
                    'CRM básico para seguimiento'
                ],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'imagen_url' => '/images/capacitaciones/prospeccion.jpg',
                'orden' => 4,
                'activo' => true,
                'lider_id' => $liderId,
                'asignaciones' => [],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'titulo' => 'Negociación Avanzada',
                'descripcion' => 'Desarrolla habilidades de negociación para cerrar acuerdos ganar-ganar con tus clientes.',
                'contenido' => 'Contenido avanzado: 1) Preparación para la negociación, 2) Tácticas y estrategias, 3) Manejo del poder en la negociación, 4) Creación de valor, 5) Cierre y acuerdos.',
                'duracion' => '5 horas',
                'nivel' => 'Avanzado',
                'categoria' => 'Ventas',
                'icono' => 'fa-balance-scale',
                'objetivos' => [
                    'Cerrar negociaciones complejas exitosamente',
                    'Mantener márgenes de ganancia óptimos',
                    'Crear acuerdos beneficiosos para ambas partes',
                    'Manejar objeciones de precio con confianza'
                ],
                'recursos' => [
                    'Framework de negociación paso a paso',
                    'Matriz de concesiones',
                    'Casos de negociación reales',
                    'Simulaciones interactivas'
                ],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'imagen_url' => '/images/capacitaciones/negociacion.jpg',
                'orden' => 5,
                'activo' => true,
                'lider_id' => $liderId,
                'asignaciones' => [],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'titulo' => 'Marketing Digital para Vendedores',
                'descripcion' => 'Utiliza herramientas digitales y redes sociales para potenciar tus ventas.',
                'contenido' => 'Aprende: 1) Fundamentos de marketing digital, 2) Uso de redes sociales para ventas, 3) Email marketing efectivo, 4) Personal branding, 5) Análisis de métricas digitales.',
                'duracion' => '3 horas',
                'nivel' => 'Intermedio',
                'categoria' => 'Marketing',
                'icono' => 'fa-bullhorn',
                'objetivos' => [
                    'Generar leads a través de canales digitales',
                    'Construir presencia profesional en redes',
                    'Implementar campañas de email marketing',
                    'Medir ROI de acciones digitales'
                ],
                'recursos' => [
                    'Guía de redes sociales para ventas',
                    'Plantillas de contenido',
                    'Herramientas digitales gratuitas',
                    'Calendario de contenidos'
                ],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'imagen_url' => '/images/capacitaciones/marketing-digital.jpg',
                'orden' => 6,
                'activo' => true,
                'lider_id' => $liderId,
                'asignaciones' => [],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'titulo' => 'Gestión del Tiempo y Productividad',
                'descripcion' => 'Optimiza tu tiempo y aumenta tu productividad como vendedor.',
                'contenido' => 'Módulo cubre: 1) Planificación de actividades de venta, 2) Priorización de tareas, 3) Gestión de agenda, 4) Eliminación de distractores, 5) Hábitos de alto rendimiento.',
                'duracion' => '2 horas',
                'nivel' => 'Básico',
                'categoria' => 'Productividad',
                'icono' => 'fa-clock',
                'objetivos' => [
                    'Aumentar visitas efectivas en 40%',
                    'Reducir tiempo en tareas administrativas',
                    'Implementar rutinas de alto rendimiento',
                    'Alcanzar metas con menos estrés'
                ],
                'recursos' => [
                    'Plantilla de planificación semanal',
                    'Matriz de Eisenhower para ventas',
                    'Apps de productividad recomendadas',
                    'Técnica Pomodoro adaptada a ventas'
                ],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'imagen_url' => '/images/capacitaciones/productividad.jpg',
                'orden' => 7,
                'activo' => true,
                'lider_id' => $liderId,
                'asignaciones' => [],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'titulo' => 'Inteligencia Emocional en Ventas',
                'descripcion' => 'Desarrolla tu inteligencia emocional para conectar mejor con clientes y manejar la presión.',
                'contenido' => 'Contenido: 1) Autoconocimiento emocional, 2) Autorregulación, 3) Empatía con el cliente, 4) Manejo del rechazo, 5) Motivación intrínseca.',
                'duracion' => '2.5 horas',
                'nivel' => 'Avanzado',
                'categoria' => 'Desarrollo Personal',
                'icono' => 'fa-heart',
                'objetivos' => [
                    'Mejorar relaciones con clientes',
                    'Manejar el estrés y la presión',
                    'Aumentar resiliencia ante rechazos',
                    'Mantener motivación constante'
                ],
                'recursos' => [
                    'Test de inteligencia emocional',
                    'Ejercicios de autoconocimiento',
                    'Técnicas de manejo de estrés',
                    'Diario de reflexión emocional'
                ],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'imagen_url' => '/images/capacitaciones/inteligencia-emocional.jpg',
                'orden' => 8,
                'activo' => true,
                'lider_id' => $liderId,
                'asignaciones' => [],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        $creados = 0;
        $omitidos = 0;

        foreach ($capacitaciones as $capacitacion) {
            // Verificar si ya existe una capacitación con el mismo título para este líder
            $existe = Capacitacion::where('lider_id', $liderId)
                                   ->where('titulo', $capacitacion['titulo'])
                                   ->exists();

            if (!$existe) {
                Capacitacion::create($capacitacion);
                $creados++;
            } else {
                $omitidos++;
            }
        }

        if ($creados > 0) {
            $this->command->info('✓ Se crearon ' . $creados . ' módulos de capacitación');
            $this->command->info('  Asignados al líder: ' . $lider->name . ' (' . $lider->email . ')');
        }

        if ($omitidos > 0) {
            $this->command->warn('⚠ Se omitieron ' . $omitidos . ' módulos porque ya existían');
        }
    }
}
