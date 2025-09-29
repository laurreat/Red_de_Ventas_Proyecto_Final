<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\User;
use App\Models\Pedido;

class NotificationService
{
    /**
     * Crear una notificación para un usuario específico
     */
    public static function crear($user_id, $tipo, $titulo, $mensaje, $data = [], $prioridad = 'normal', $fecha_expiracion = null)
    {
        return Notificacion::create([
            'user_id' => $user_id,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'datos_adicionales' => $data,
            'prioridad' => $prioridad,
            'fecha_expiracion' => $fecha_expiracion,
            'leida' => false
        ]);
    }

    /**
     * Enviar notificación a múltiples usuarios por rol
     */
    public static function enviarPorRol($rol, $tipo, $titulo, $mensaje, $data = [], $prioridad = 'normal')
    {
        $usuarios = User::where('rol', $rol)->where('activo', true)->get();

        foreach ($usuarios as $usuario) {
            self::crear($usuario->id, $tipo, $titulo, $mensaje, $data, $prioridad);
        }

        return $usuarios->count();
    }

    /**
     * Notificar nuevo pedido a administradores y líderes
     */
    public static function nuevoPedido($pedido)
    {
        $cliente = $pedido->cliente ?? null;
        $clienteNombre = $cliente ? "{$cliente->name} {$cliente->apellidos}" : 'Cliente desconocido';

        $titulo = "Nuevo Pedido #{$pedido->numero_pedido}";
        $mensaje = "Se ha creado un nuevo pedido de {$clienteNombre} por $" . number_format($pedido->total, 2);

        $data = [
            'pedido_id' => $pedido->id,
            'numero_pedido' => $pedido->numero_pedido,
            'total' => $pedido->total,
            'cliente_nombre' => $clienteNombre
        ];

        // Notificar a administradores
        self::enviarPorRol('administrador', 'pedido', $titulo, $mensaje, $data, 'alta');

        // Notificar a líderes
        self::enviarPorRol('lider', 'pedido', $titulo, $mensaje, $data, 'normal');
    }

    /**
     * Notificar cambio de estado de pedido al cliente
     */
    public static function cambioEstadoPedido($pedido, $estadoAnterior)
    {
        if (!$pedido->cliente) return;

        $estados = [
            'pendiente' => 'Pendiente',
            'confirmado' => 'Confirmado',
            'preparando' => 'Preparando',
            'enviado' => 'Enviado',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado'
        ];

        $estadoActual = $estados[$pedido->estado] ?? $pedido->estado;
        $titulo = "Pedido #{$pedido->numero_pedido} - {$estadoActual}";

        $mensajes = [
            'confirmado' => 'Tu pedido ha sido confirmado y está siendo procesado.',
            'preparando' => 'Tu pedido está siendo preparado para el envío.',
            'enviado' => 'Tu pedido ha sido enviado y está en camino.',
            'entregado' => '¡Tu pedido ha sido entregado exitosamente!',
            'cancelado' => 'Tu pedido ha sido cancelado. Si tienes dudas, contáctanos.'
        ];

        $mensaje = $mensajes[$pedido->estado] ?? "El estado de tu pedido ha cambiado a {$estadoActual}.";

        $data = [
            'pedido_id' => $pedido->id,
            'numero_pedido' => $pedido->numero_pedido,
            'estado_anterior' => $estadoAnterior,
            'estado_actual' => $pedido->estado
        ];

        $prioridad = $pedido->estado === 'entregado' ? 'alta' : 'normal';

        self::crear($pedido->cliente->id, 'pedido', $titulo, $mensaje, $data, $prioridad);
    }

    /**
     * Notificar nueva venta a vendedor y su líder
     */
    public static function nuevaVenta($pedido)
    {
        if (!$pedido->vendedor) return;

        $titulo = "Nueva Venta Realizada";
        $mensaje = "Has realizado una venta por $" . number_format($pedido->total, 2);

        $data = [
            'pedido_id' => $pedido->id,
            'numero_pedido' => $pedido->numero_pedido,
            'total' => $pedido->total,
            'comision_estimada' => $pedido->total * 0.10 // Ejemplo: 10% de comisión
        ];

        // Notificar al vendedor
        self::crear($pedido->vendedor->id, 'venta', $titulo, $mensaje, $data, 'alta');

        // Notificar al líder si existe
        if ($pedido->vendedor->referido_por) {
            $lider = User::find($pedido->vendedor->referido_por);
            if ($lider && $lider->rol === 'lider') {
                $tituloLider = "Venta del Equipo";
                $mensajeLider = "Tu vendedor {$pedido->vendedor->name} realizó una venta por $" . number_format($pedido->total, 2);

                self::crear($lider->id, 'venta', $tituloLider, $mensajeLider, $data, 'normal');
            }
        }
    }

    /**
     * Notificar nuevo usuario registrado
     */
    public static function nuevoUsuario($usuario)
    {
        $titulo = "Nuevo Usuario Registrado";
        $mensaje = "Se ha registrado un nuevo {$usuario->rol}: {$usuario->name} {$usuario->apellidos}";

        $data = [
            'usuario_id' => $usuario->id,
            'nombre' => $usuario->name,
            'apellidos' => $usuario->apellidos,
            'rol' => $usuario->rol,
            'email' => $usuario->email
        ];

        // Notificar a administradores
        self::enviarPorRol('administrador', 'usuario', $titulo, $mensaje, $data, 'normal');

        // Si es vendedor y tiene un líder, notificar al líder
        if ($usuario->rol === 'vendedor' && $usuario->referido_por) {
            $lider = User::find($usuario->referido_por);
            if ($lider && $lider->rol === 'lider') {
                $tituloLider = "Nuevo Vendedor en tu Equipo";
                $mensajeLider = "Se ha unido {$usuario->name} {$usuario->apellidos} a tu equipo de ventas";

                self::crear($lider->id, 'usuario', $tituloLider, $mensajeLider, $data, 'alta');
            }
        }
    }

    /**
     * Notificar comisión generada
     */
    public static function comisionGenerada($usuario, $monto, $concepto = 'Venta')
    {
        $titulo = "Nueva Comisión Generada";
        $mensaje = "Has generado una comisión de $" . number_format($monto, 2) . " por {$concepto}";

        $data = [
            'monto' => $monto,
            'concepto' => $concepto,
            'fecha' => now()->toDateString()
        ];

        self::crear($usuario->id, 'comision', $titulo, $mensaje, $data, 'alta');
    }

    /**
     * Notificar meta alcanzada
     */
    public static function metaAlcanzada($usuario, $metaType = 'mensual')
    {
        $titulo = "¡Meta Alcanzada!";
        $mensaje = "¡Felicidades! Has alcanzado tu meta {$metaType}";

        $data = [
            'tipo_meta' => $metaType,
            'fecha_logro' => now()->toDateString()
        ];

        self::crear($usuario->id, 'sistema', $titulo, $mensaje, $data, 'alta');
    }

    /**
     * Notificar problema del sistema a administradores
     */
    public static function problemaSystem($titulo, $descripcion, $prioridad = 'alta')
    {
        $mensaje = "Se ha detectado un problema en el sistema: {$descripcion}";

        $data = [
            'timestamp' => now()->toDateTimeString(),
            'descripcion' => $descripcion
        ];

        self::enviarPorRol('administrador', 'sistema', $titulo, $mensaje, $data, $prioridad);
    }

    /**
     * Notificar respaldo creado
     */
    public static function respaldoCreado($nombreArchivo, $tipo)
    {
        $titulo = "Respaldo Creado";
        $mensaje = "Se ha creado un nuevo respaldo del tipo '{$tipo}': {$nombreArchivo}";

        $data = [
            'archivo' => $nombreArchivo,
            'tipo' => $tipo,
            'fecha' => now()->toDateTimeString()
        ];

        self::enviarPorRol('administrador', 'sistema', $titulo, $mensaje, $data, 'normal');
    }

    /**
     * Limpiar notificaciones expiradas (para ejecutar por cron)
     */
    public static function limpiarExpiradas()
    {
        $count = Notificacion::where('fecha_expiracion', '<', now())
            ->whereNotNull('fecha_expiracion')
            ->count();

        Notificacion::where('fecha_expiracion', '<', now())
            ->whereNotNull('fecha_expiracion')
            ->delete();

        return $count;
    }

    /**
     * Crear notificaciones de prueba (para testing)
     */
    public static function crearNotificacionesPrueba($userId)
    {
        $notificaciones = [
            [
                'tipo' => 'pedido',
                'titulo' => 'Nuevo Pedido #12345',
                'mensaje' => 'Se ha creado un nuevo pedido de Juan Pérez por $150.000',
                'prioridad' => 'alta'
            ],
            [
                'tipo' => 'venta',
                'titulo' => 'Venta Realizada',
                'mensaje' => 'Has realizado una venta por $89.500 - Comisión: $8.950',
                'prioridad' => 'alta'
            ],
            [
                'tipo' => 'usuario',
                'titulo' => 'Nuevo Vendedor',
                'mensaje' => 'María González se ha unido a tu equipo de ventas',
                'prioridad' => 'normal'
            ],
            [
                'tipo' => 'comision',
                'titulo' => 'Comisión Generada',
                'mensaje' => 'Has generado una comisión de $12.500 por venta directa',
                'prioridad' => 'alta'
            ],
            [
                'tipo' => 'sistema',
                'titulo' => '¡Meta Alcanzada!',
                'mensaje' => 'Felicidades! Has alcanzado tu meta mensual de ventas',
                'prioridad' => 'alta'
            ]
        ];

        foreach ($notificaciones as $notif) {
            self::crear(
                $userId,
                $notif['tipo'],
                $notif['titulo'],
                $notif['mensaje'],
                ['test' => true],
                $notif['prioridad']
            );
        }

        return count($notificaciones);
    }
}