<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    protected $errorCode;
    protected $details;

    public function __construct($message = "", $errorCode = null, $details = null, $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'error_code' => $this->getErrorCode(),
                'details' => $this->getDetails()
            ], $this->getCode());
        }

        return redirect()->back()
            ->withErrors(['error' => $this->getMessage()])
            ->withInput();
    }

    // Métodos estáticos para errores comunes de negocio

    public static function vendedorNoAutorizado()
    {
        return new self(
            'Este vendedor no está autorizado para realizar esta acción',
            'VENDEDOR_NO_AUTORIZADO',
            null,
            403
        );
    }

    public static function comisionYaCalculada($periodo)
    {
        return new self(
            "Las comisiones para el período {$periodo} ya han sido calculadas",
            'COMISION_YA_CALCULADA',
            ['periodo' => $periodo],
            409
        );
    }

    public static function pedidoNoEditable($estado)
    {
        return new self(
            "No se puede editar un pedido en estado '{$estado}'",
            'PEDIDO_NO_EDITABLE',
            ['estado_actual' => $estado],
            409
        );
    }

    public static function stockInsuficiente($producto, $disponible, $solicitado)
    {
        return new self(
            "Stock insuficiente para el producto '{$producto}'. Disponible: {$disponible}, Solicitado: {$solicitado}",
            'STOCK_INSUFICIENTE',
            [
                'producto' => $producto,
                'disponible' => $disponible,
                'solicitado' => $solicitado
            ],
            409
        );
    }

    public static function referidoDuplicado($email)
    {
        return new self(
            "El usuario con email '{$email}' ya ha sido referido anteriormente",
            'REFERIDO_DUPLICADO',
            ['email' => $email],
            409
        );
    }

    public static function ventaNoAutorizada($razon)
    {
        return new self(
            "Venta no autorizada: {$razon}",
            'VENTA_NO_AUTORIZADA',
            ['razon' => $razon],
            403
        );
    }

    public static function configuracionNoValida($clave, $valor)
    {
        return new self(
            "El valor '{$valor}' no es válido para la configuración '{$clave}'",
            'CONFIGURACION_NO_VALIDA',
            ['clave' => $clave, 'valor' => $valor],
            400
        );
    }

    public static function usuarioInactivo($email)
    {
        return new self(
            "El usuario '{$email}' está inactivo y no puede realizar esta acción",
            'USUARIO_INACTIVO',
            ['email' => $email],
            403
        );
    }

    public static function limitePedidosExcedido($limite)
    {
        return new self(
            "Has excedido el límite de {$limite} pedidos por día",
            'LIMITE_PEDIDOS_EXCEDIDO',
            ['limite_diario' => $limite],
            429
        );
    }

    public static function zonaEntregaNoValida($zona)
    {
        return new self(
            "La zona de entrega '{$zona}' no está disponible",
            'ZONA_ENTREGA_NO_VALIDA',
            ['zona' => $zona],
            400
        );
    }

    public static function horarioEntregaNoValido($hora)
    {
        return new self(
            "El horario '{$hora}' no está disponible para entregas",
            'HORARIO_ENTREGA_NO_VALIDO',
            ['hora_solicitada' => $hora],
            400
        );
    }

    public static function metodoPagoNoValido($metodo)
    {
        return new self(
            "El método de pago '{$metodo}' no está disponible",
            'METODO_PAGO_NO_VALIDO',
            ['metodo' => $metodo],
            400
        );
    }

    public static function montoMinimoNoAlcanzado($actual, $minimo)
    {
        return new self(
            "El monto del pedido ({$actual}) no alcanza el mínimo requerido ({$minimo})",
            'MONTO_MINIMO_NO_ALCANZADO',
            ['monto_actual' => $actual, 'monto_minimo' => $minimo],
            400
        );
    }

    public static function archivoNoValido($tipo, $tamano = null)
    {
        $mensaje = "El archivo de tipo '{$tipo}' no es válido";
        $detalles = ['tipo' => $tipo];

        if ($tamano) {
            $mensaje .= " o excede el tamaño máximo permitido";
            $detalles['tamano'] = $tamano;
        }

        return new self(
            $mensaje,
            'ARCHIVO_NO_VALIDO',
            $detalles,
            400
        );
    }

    public static function operacionNoPeriodo($operacion, $fechaInicio, $fechaFin)
    {
        return new self(
            "No se puede realizar la operación '{$operacion}' fuera del período válido ({$fechaInicio} - {$fechaFin})",
            'OPERACION_FUERA_DE_PERIODO',
            [
                'operacion' => $operacion,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ],
            400
        );
    }
}