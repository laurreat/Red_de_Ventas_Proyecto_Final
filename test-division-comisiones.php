<?php

/**
 * Test para verificar la lógica de división de comisiones
 */

// Simulación de comisiones
$comisiones = [
    ['id' => 1, 'monto' => 21200, 'estado' => 'pendiente'],
    ['id' => 2, 'monto' => 29400, 'estado' => 'pendiente']
];

$montoSolicitado = 50000;
$montoRestante = $montoSolicitado;
$comisionesAfectadas = [];
$comisionesCreadas = [];

echo "=== SIMULACIÓN DE DIVISIÓN DE COMISIONES ===\n\n";
echo "Comisiones disponibles:\n";
foreach ($comisiones as $c) {
    echo "  - Comisión #{$c['id']}: \${$c['monto']}\n";
}
echo "\nMonto solicitado: \${$montoSolicitado}\n";
echo "Total disponible: \$" . array_sum(array_column($comisiones, 'monto')) . "\n\n";

echo "=== PROCESAMIENTO ===\n\n";

foreach ($comisiones as $index => $comision) {
    if ($montoRestante <= 0) {
        echo "✅ Monto completo alcanzado\n";
        break;
    }

    $montoComision = $comision['monto'];
    echo "Procesando Comisión #{$comision['id']} (\${$montoComision})...\n";

    if ($montoComision <= $montoRestante) {
        // La comisión completa cabe
        echo "  ➡️ Usada completamente: \${$montoComision}\n";
        $comisiones[$index]['estado'] = 'en_proceso';
        $montoRestante -= $montoComision;
        $comisionesAfectadas[] = $comision['id'];
        echo "  💰 Monto restante: \${$montoRestante}\n\n";
    } else {
        // Dividir la comisión
        $montoSobrante = $montoComision - $montoRestante;
        echo "  ✂️ DIVISIÓN REQUERIDA:\n";
        echo "     • Monto usado: \${$montoRestante}\n";
        echo "     • Monto sobrante: \${$montoSobrante}\n";
        
        // Actualizar comisión original
        $comisiones[$index]['monto'] = $montoRestante;
        $comisiones[$index]['estado'] = 'en_proceso';
        $comisiones[$index]['dividida'] = true;
        $comisiones[$index]['monto_original'] = $montoComision;
        
        // Crear nueva comisión con el sobrante
        $nuevaComision = [
            'id' => 'nuevo_' . ($index + 1),
            'monto' => $montoSobrante,
            'estado' => 'pendiente',
            'descripcion' => "Sobrante de comisión #{$comision['id']}",
            'dividida_desde' => $comision['id']
        ];
        $comisionesCreadas[] = $nuevaComision;
        
        $comisionesAfectadas[] = $comision['id'];
        $montoRestante = 0;
        echo "  ✅ Nueva comisión creada con sobrante\n\n";
        break;
    }
}

echo "=== RESULTADO FINAL ===\n\n";
echo "Comisiones procesadas: " . count($comisionesAfectadas) . "\n";
echo "Nuevas comisiones creadas: " . count($comisionesCreadas) . "\n";
echo "Monto restante sin usar: \${$montoRestante}\n\n";

echo "Estado final de comisiones:\n";
foreach ($comisiones as $c) {
    $divisonInfo = isset($c['dividida']) && $c['dividida'] 
        ? " (DIVIDIDA desde \${$c['monto_original']})" 
        : "";
    echo "  - Comisión #{$c['id']}: \${$c['monto']} - {$c['estado']}{$divisonInfo}\n";
}

if (!empty($comisionesCreadas)) {
    echo "\nNuevas comisiones:\n";
    foreach ($comisionesCreadas as $c) {
        echo "  - Comisión #{$c['id']}: \${$c['monto']} - {$c['estado']} - {$c['descripcion']}\n";
    }
}

echo "\n=== VERIFICACIÓN ===\n\n";
$totalUsado = $montoSolicitado - $montoRestante;
echo "✅ Monto solicitado: \${$montoSolicitado}\n";
echo "✅ Monto usado: \${$totalUsado}\n";
echo "✅ Diferencia: \${$montoRestante}\n";

if ($totalUsado == $montoSolicitado && $montoRestante == 0) {
    echo "\n🎉 ¡PRUEBA EXITOSA! El sistema dividió correctamente las comisiones.\n";
} else {
    echo "\n❌ ERROR: El cálculo no coincide.\n";
}

echo "\n";
