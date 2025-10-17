<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Porcentajes de Comisiones
    |--------------------------------------------------------------------------
    |
    | Configuración de los porcentajes de comisiones para el sistema de ventas.
    | Estos valores se aplican automáticamente cuando un pedido se marca como
    | "entregado" o "confirmado".
    |
    */

    // Comisión por venta directa (vendedor que realiza la venta)
    'venta_directa' => env('COMISION_VENTA_DIRECTA', 10), // 10%

    // Comisión por referido (quien refirió al vendedor)
    'referido' => env('COMISION_REFERIDO', 5), // 5%

    // Comisión de liderazgo (para niveles superiores en la red)
    'liderazgo' => env('COMISION_LIDERAZGO', 3), // 3%

    // Número máximo de niveles para comisiones de liderazgo
    'max_niveles_liderazgo' => env('MAX_NIVELES_LIDERAZGO', 3), // 3 niveles

    /*
    |--------------------------------------------------------------------------
    | Estados de Pedidos que Generan Comisiones
    |--------------------------------------------------------------------------
    |
    | Estados de pedidos que disparan el cálculo automático de comisiones.
    |
    */
    'estados_comision' => ['entregado', 'confirmado'],

    /*
    |--------------------------------------------------------------------------
    | Roles que Pueden Recibir Comisiones
    |--------------------------------------------------------------------------
    */
    'roles_comision_venta' => ['vendedor', 'lider', 'administrador'],
    'roles_comision_liderazgo' => ['lider', 'administrador'],
];
