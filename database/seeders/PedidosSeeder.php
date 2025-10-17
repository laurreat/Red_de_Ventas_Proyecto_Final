<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Producto;
use Carbon\Carbon;

class PedidosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🛒 Iniciando generación de pedidos...');

        // Obtener usuarios necesarios
        $vendedores = User::where('rol', 'vendedor')->get();
        $clientes = User::where('rol', 'cliente')->get();
        $productos = Producto::where('activo', true)->get();

        // Verificar que existan datos
        if ($vendedores->isEmpty()) {
            $this->command->warn('No hay vendedores. Creando vendedores de prueba...');
            $vendedores = $this->crearVendedores();
        }

        if ($clientes->isEmpty()) {
            $this->command->warn('No hay clientes. Creando clientes de prueba...');
            $clientes = $this->crearClientes();
        }

        if ($productos->isEmpty()) {
            $this->command->warn('No hay productos. Creando productos de prueba...');
            $productos = $this->crearProductos();
        }

        // Limpiar pedidos existentes
        Pedido::truncate();
        $this->command->info('🗑️  Pedidos anteriores eliminados');

        // Estados posibles
        $estados = ['pendiente', 'confirmado', 'en_preparacion', 'enviado', 'entregado', 'cancelado'];

        // Generar pedidos para los últimos 6 meses
        $pedidosCreados = 0;
        $totalIngresos = 0;

        for ($mes = 5; $mes >= 0; $mes--) {
            $fecha = Carbon::now()->subMonths($mes);
            $numPedidos = rand(8, 15); // Entre 8 y 15 pedidos por mes

            for ($i = 0; $i < $numPedidos; $i++) {
                $vendedor = $vendedores->random();
                $cliente = $clientes->random();

                // Fecha aleatoria dentro del mes
                $fechaPedido = $fecha->copy()->addDays(rand(1, 28))->addHours(rand(8, 20))->addMinutes(rand(0, 59));

                // Determinar estado según antigüedad
                $diasDesdeCreacion = $fechaPedido->diffInDays(Carbon::now());
                if ($diasDesdeCreacion > 30) {
                    $estado = collect(['entregado', 'cancelado'])->random();
                } elseif ($diasDesdeCreacion > 15) {
                    $estado = collect(['en_preparacion', 'enviado', 'entregado'])->random();
                } elseif ($diasDesdeCreacion > 7) {
                    $estado = collect(['confirmado', 'en_preparacion'])->random();
                } else {
                    $estado = collect(['pendiente', 'confirmado'])->random();
                }

                // Seleccionar productos aleatorios (entre 1 y 5)
                $numProductos = rand(1, 5);
                $productosSeleccionados = $productos->random(min($numProductos, $productos->count()));

                $detalles = [];
                $subtotal = 0;

                foreach ($productosSeleccionados as $producto) {
                    $cantidad = rand(1, 3);
                    $precioUnitario = $producto->precio;
                    $totalDetalle = $cantidad * $precioUnitario;

                    $detalles[] = [
                        'producto_id' => $producto->id,
                        'producto_nombre' => $producto->nombre,
                        'producto_codigo' => $producto->codigo ?? 'N/A',
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $totalDetalle,
                        'created_at' => $fechaPedido,
                        'updated_at' => $fechaPedido
                    ];

                    $subtotal += $totalDetalle;
                }

                // Calcular descuento (10% de probabilidad)
                $descuento = rand(1, 10) === 1 ? $subtotal * 0.1 : 0;

                // Calcular total
                $total = $subtotal - $descuento;

                // Crear pedido
                $pedido = Pedido::create([
                    'numero_pedido' => 'PED-' . str_pad($pedidosCreados + 1, 8, '0', STR_PAD_LEFT),
                    'cliente_id' => $cliente->id,
                    'cliente_data' => [
                        'name' => $cliente->name,
                        'email' => $cliente->email,
                        'telefono' => $cliente->telefono ?? '3001234567'
                    ],
                    'vendedor_id' => $vendedor->id,
                    'vendedor_data' => [
                        'name' => $vendedor->name,
                        'email' => $vendedor->email
                    ],
                    'detalles_embebidos' => $detalles,
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'total_final' => $total,
                    'estado' => $estado,
                    'notas' => $this->generarNotas($estado),
                    'direccion_envio' => $this->generarDireccion(),
                    'metodo_pago' => collect(['efectivo', 'transferencia', 'tarjeta'])->random(),
                    'created_at' => $fechaPedido,
                    'updated_at' => $fechaPedido
                ]);

                $pedidosCreados++;
                if ($estado === 'entregado') {
                    $totalIngresos += $total;
                }
            }
        }

        $this->command->info("✅ Seeder de pedidos completado!");
        $this->command->info("📦 Pedidos creados: {$pedidosCreados}");
        $this->command->info("💰 Ingresos totales (pedidos entregados): $" . number_format($totalIngresos, 0, ',', '.'));
    }

    private function crearVendedores()
    {
        $vendedores = collect();

        for ($i = 1; $i <= 5; $i++) {
            $vendedores->push(User::create([
                'name' => "Vendedor Test {$i}",
                'email' => "vendedor{$i}@example.com",
                'password' => bcrypt('password'),
                'rol' => 'vendedor',
                'activo' => true
            ]));
        }

        $this->command->info("👤 {$vendedores->count()} vendedores creados");
        return $vendedores;
    }

    private function crearClientes()
    {
        $clientes = collect();
        $nombres = [
            'Juan Pérez', 'María García', 'Carlos López', 'Ana Martínez', 'Pedro Rodríguez',
            'Laura Fernández', 'Diego González', 'Carmen Sánchez', 'Miguel Torres', 'Isabel Ramírez'
        ];

        for ($i = 0; $i < 10; $i++) {
            $clientes->push(User::create([
                'name' => $nombres[$i],
                'email' => "cliente" . ($i + 1) . "@example.com",
                'password' => bcrypt('password'),
                'rol' => 'cliente',
                'activo' => true,
                'telefono' => '300' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT)
            ]));
        }

        $this->command->info("👥 {$clientes->count()} clientes creados");
        return $clientes;
    }

    private function crearProductos()
    {
        $productos = collect();
        $productosData = [
            ['nombre' => 'Arepa de Queso', 'precio' => 3500, 'codigo' => 'AREPA-001'],
            ['nombre' => 'Arepa con Carne', 'precio' => 8000, 'codigo' => 'AREPA-002'],
            ['nombre' => 'Arepa Mixta', 'precio' => 10000, 'codigo' => 'AREPA-003'],
            ['nombre' => 'Jugo Natural', 'precio' => 4000, 'codigo' => 'BEB-001'],
            ['nombre' => 'Café', 'precio' => 2500, 'codigo' => 'BEB-002'],
            ['nombre' => 'Empanada', 'precio' => 2000, 'codigo' => 'EMP-001'],
            ['nombre' => 'Papa Rellena', 'precio' => 3000, 'codigo' => 'PAP-001'],
            ['nombre' => 'Chicharrón', 'precio' => 5000, 'codigo' => 'CHI-001']
        ];

        foreach ($productosData as $data) {
            $productos->push(Producto::create([
                'nombre' => $data['nombre'],
                'codigo' => $data['codigo'],
                'precio' => $data['precio'],
                'stock' => rand(50, 200),
                'activo' => true,
                'descripcion' => 'Producto tradicional llanero de excelente calidad'
            ]));
        }

        $this->command->info("🍴 {$productos->count()} productos creados");
        return $productos;
    }

    private function generarNotas($estado)
    {
        $notas = [
            'pendiente' => 'Pedido pendiente de confirmación',
            'confirmado' => 'Pedido confirmado, en espera de preparación',
            'en_preparacion' => 'Pedido en proceso de preparación',
            'enviado' => 'Pedido enviado, en camino al cliente',
            'entregado' => 'Pedido entregado satisfactoriamente',
            'cancelado' => 'Pedido cancelado por solicitud del cliente'
        ];

        return $notas[$estado] ?? '';
    }

    private function generarDireccion()
    {
        $calles = ['Calle 10', 'Carrera 15', 'Avenida 20', 'Diagonal 25', 'Transversal 30'];
        $numeros = ['#' . rand(10, 99) . '-' . rand(10, 99)];
        $barrios = ['Centro', 'Norte', 'Sur', 'Kennedy', 'Suba', 'Usaquén'];

        return $calles[array_rand($calles)] . ' ' . $numeros[0] . ', Barrio ' . $barrios[array_rand($barrios)] . ', Bogotá';
    }
}
