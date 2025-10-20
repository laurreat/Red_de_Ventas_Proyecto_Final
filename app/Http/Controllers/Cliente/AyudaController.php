<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Configuracion;

class AyudaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Mostrar módulo de ayuda
     */
    public function index()
    {
        $user = Auth::user();
        
        // Preguntas frecuentes
        $faqs = $this->getFAQs();
        
        // Información de contacto
        $contacto = $this->getInformacionContacto();
        
        // Tutoriales y guías
        $tutoriales = $this->getTutoriales();

        return view('cliente.ayuda.index', compact('user', 'faqs', 'contacto', 'tutoriales'));
    }

    /**
     * Obtener preguntas frecuentes
     */
    private function getFAQs()
    {
        return [
            [
                'categoria' => 'Pedidos',
                'preguntas' => [
                    [
                        'pregunta' => '¿Cómo puedo hacer un pedido?',
                        'respuesta' => 'Ve a la sección "Crear Pedido", selecciona los productos que deseas, completa la información de entrega y confirma tu pedido.'
                    ],
                    [
                        'pregunta' => '¿Puedo cancelar un pedido?',
                        'respuesta' => 'Sí, puedes cancelar un pedido mientras esté en estado "Pendiente" o "Confirmado". Ve a "Mis Pedidos", selecciona el pedido y haz clic en "Cancelar Pedido".'
                    ],
                    [
                        'pregunta' => '¿Cuánto tarda la entrega?',
                        'respuesta' => 'El tiempo de entrega estimado es de 3 a 5 días hábiles, dependiendo de tu ubicación. Recibirás notificaciones sobre el estado de tu pedido.'
                    ],
                ]
            ],
            [
                'categoria' => 'Pagos',
                'preguntas' => [
                    [
                        'pregunta' => '¿Qué métodos de pago aceptan?',
                        'respuesta' => 'Aceptamos efectivo contra entrega, transferencia bancaria y tarjetas de crédito/débito.'
                    ],
                    [
                        'pregunta' => '¿Debo pagar al momento del pedido?',
                        'respuesta' => 'Depende del método de pago. Para transferencia y tarjeta, el pago es inmediato. Para efectivo, pagas al recibir tu pedido.'
                    ],
                    [
                        'pregunta' => '¿Es seguro pagar en línea?',
                        'respuesta' => 'Sí, utilizamos conexiones seguras (SSL) y procesadores de pago certificados para proteger tus datos.'
                    ],
                ]
            ],
            [
                'categoria' => 'Cuenta',
                'preguntas' => [
                    [
                        'pregunta' => '¿Cómo actualizo mi información personal?',
                        'respuesta' => 'Ve a "Mi Perfil" y haz clic en "Editar Información". Actualiza los datos que necesites y guarda los cambios.'
                    ],
                    [
                        'pregunta' => '¿Puedo cambiar mi contraseña?',
                        'respuesta' => 'Sí, en "Mi Perfil" encontrarás la opción "Cambiar Contraseña". Necesitarás tu contraseña actual para confirmar el cambio.'
                    ],
                    [
                        'pregunta' => '¿Cómo funciona el programa de referidos?',
                        'respuesta' => 'Comparte tu link de referido con amigos y familiares. Cuando se registren y realicen compras, podrás ganar comisiones. Ve a "Mis Referidos" para más detalles.'
                    ],
                ]
            ],
            [
                'categoria' => 'Productos',
                'preguntas' => [
                    [
                        'pregunta' => '¿Cómo sé si un producto tiene stock?',
                        'respuesta' => 'Los productos disponibles muestran la cantidad en stock. Si un producto está agotado, aparecerá como "Sin stock".'
                    ],
                    [
                        'pregunta' => '¿Puedo devolver un producto?',
                        'respuesta' => 'Sí, tenemos política de devolución. Contacta a soporte dentro de los primeros 7 días después de recibir tu pedido.'
                    ],
                ]
            ],
        ];
    }

    /**
     * Obtener información de contacto
     */
    private function getInformacionContacto()
    {
        try {
            $config = Configuracion::first();
            
            return [
                'empresa' => $config->nombre_empresa ?? config('app.name'),
                'email' => $config->email_contacto ?? 'contacto@empresa.com',
                'telefono' => $config->telefono_contacto ?? '+57 300 123 4567',
                'whatsapp' => $config->whatsapp_contacto ?? '+57 300 123 4567',
                'direccion' => $config->direccion ?? 'Calle 123 #45-67, Bogotá, Colombia',
                'horario' => $config->horario_atencion ?? 'Lunes a Viernes: 8:00 AM - 6:00 PM | Sábados: 9:00 AM - 2:00 PM',
            ];
        } catch (\Exception $e) {
            return [
                'empresa' => config('app.name'),
                'email' => 'contacto@empresa.com',
                'telefono' => '+57 300 123 4567',
                'whatsapp' => '+57 300 123 4567',
                'direccion' => 'Calle 123 #45-67, Bogotá, Colombia',
                'horario' => 'Lunes a Viernes: 8:00 AM - 6:00 PM | Sábados: 9:00 AM - 2:00 PM',
            ];
        }
    }

    /**
     * Obtener tutoriales
     */
    private function getTutoriales()
    {
        return [
            [
                'titulo' => 'Cómo hacer tu primer pedido',
                'descripcion' => 'Aprende paso a paso cómo realizar un pedido en nuestra plataforma.',
                'icono' => 'bi-cart-check',
                'url' => '#tutorial-pedido',
            ],
            [
                'titulo' => 'Cómo usar tu link de referido',
                'descripcion' => 'Descubre cómo compartir tu link y ganar comisiones por referidos.',
                'icono' => 'bi-share',
                'url' => '#tutorial-referidos',
            ],
            [
                'titulo' => 'Gestiona tu perfil',
                'descripcion' => 'Aprende a actualizar tu información personal y configuración.',
                'icono' => 'bi-person-gear',
                'url' => '#tutorial-perfil',
            ],
            [
                'titulo' => 'Rastrear tu pedido',
                'descripcion' => 'Conoce cómo dar seguimiento al estado de tus pedidos.',
                'icono' => 'bi-truck',
                'url' => '#tutorial-rastreo',
            ],
        ];
    }

    /**
     * Enviar ticket de soporte
     */
    public function enviarTicket(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'asunto' => 'required|string|max:255',
            'categoria' => 'required|in:pedido,pago,producto,cuenta,otro',
            'mensaje' => 'required|string|min:10|max:2000',
            'pedido_id' => 'nullable|string',
        ], [
            'asunto.required' => 'El asunto es obligatorio',
            'categoria.required' => 'Debes seleccionar una categoría',
            'mensaje.required' => 'Debes escribir tu consulta',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Crear el ticket (puedes guardar en BD si tienes modelo Ticket)
            $ticketData = [
                'user_id' => $user->_id,
                'user_name' => $user->name . ' ' . ($user->apellidos ?? ''),
                'user_email' => $user->email,
                'asunto' => $request->asunto,
                'categoria' => $request->categoria,
                'mensaje' => $request->mensaje,
                'pedido_id' => $request->pedido_id,
                'fecha' => now(),
            ];

            // Enviar email a soporte (opcional)
            try {
                $contacto = $this->getInformacionContacto();
                Mail::raw(
                    "Nuevo ticket de soporte\n\n" .
                    "Usuario: {$ticketData['user_name']}\n" .
                    "Email: {$ticketData['user_email']}\n" .
                    "Categoría: {$ticketData['categoria']}\n" .
                    "Asunto: {$ticketData['asunto']}\n\n" .
                    "Mensaje:\n{$ticketData['mensaje']}",
                    function ($message) use ($contacto, $ticketData) {
                        $message->to($contacto['email'])
                                ->subject('Nuevo Ticket de Soporte: ' . $ticketData['asunto']);
                    }
                );
            } catch (\Exception $e) {
                \Log::error('Error al enviar email de ticket: ' . $e->getMessage());
            }

            \Log::info('Ticket de soporte creado', $ticketData);

            return back()->with('success', 'Tu consulta ha sido enviada. Te responderemos pronto por correo electrónico.');
        } catch (\Exception $e) {
            \Log::error('Error al crear ticket: ' . $e->getMessage());
            return back()->with('error', 'Error al enviar tu consulta. Intenta nuevamente o contáctanos directamente.');
        }
    }
}
