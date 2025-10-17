<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Comision;
use App\Models\Pedido;
use App\Models\SolicitudPago;
use Carbon\Carbon;

class ComisionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuario líder
        $lider = User::where('rol', 'lider')->first();

        if (!$lider) {
            $this->command->error('No se encontró ningún usuario con rol líder');
            return;
        }

        $this->command->info("Generando comisiones para el líder: {$lider->name}");

        // Obtener o crear vendedores referidos
        $vendedores = User::where('rol', 'vendedor')->take(5)->get();

        if ($vendedores->count() < 5) {
            $this->command->info('Creando vendedores de prueba...');
            for ($i = $vendedores->count(); $i < 5; $i++) {
                $vendedor = User::create([
                    'name' => "Vendedor Test " . ($i + 1),
                    'email' => "vendedor.test" . ($i + 1) . "@example.com",
                    'password' => bcrypt('password'),
                    'rol' => 'vendedor',
                    'activo' => true,
                    'referido_por' => $lider->id,
                    'referido_por_data' => [
                        'name' => $lider->name,
                        'email' => $lider->email
                    ]
                ]);
                $vendedores->push($vendedor);
            }
        }

        // Obtener pedidos reales de la base de datos
        $pedidos = Pedido::whereIn('estado', ['entregado', 'confirmado', 'en_preparacion'])->get();

        if ($pedidos->isEmpty()) {
            $this->command->warn('No hay pedidos en la base de datos. Ejecuta primero: php artisan db:seed --class=PedidosSeeder');
            return;
        }

        $this->command->info("📦 Usando {$pedidos->count()} pedidos reales de la base de datos...");

        // Limpiar comisiones existentes
        Comision::truncate();
        $this->command->info('Comisiones anteriores eliminadas');

        // Tipos de comisiones con sus porcentajes
        $tiposComision = [
            'venta_directa' => 10,  // 10% de comisión
            'referido' => 5,         // 5% de comisión
            'liderazgo' => 3,        // 3% de comisión
            'bono' => 0              // Monto fijo
        ];

        $comisionesCreadas = 0;
        $totalComisiones = 0;

        // Generar comisiones para los últimos 6 meses
        for ($mes = 5; $mes >= 0; $mes--) {
            $fecha = Carbon::now()->subMonths($mes);
            $numComisiones = rand(3, 8); // Entre 3 y 8 comisiones por mes

            for ($i = 0; $i < $numComisiones; $i++) {
                // Seleccionar tipo de comisión aleatoriamente
                $tipo = collect(array_keys($tiposComision))->random();
                $porcentaje = $tiposComision[$tipo];

                // Seleccionar un pedido real
                $pedido = $pedidos->random();
                $totalPedido = $pedido->total_final;
                $numeroPedido = $pedido->numero_pedido;

                $referido = $tipo !== 'venta_directa' ? $vendedores->random() : null;

                // Calcular monto
                if ($tipo === 'bono') {
                    $monto = rand(50000, 200000); // Bono entre 50k y 200k
                } else {
                    $monto = $totalPedido * ($porcentaje / 100);
                }

                // Determinar estado
                $diasDesdeCreacion = $fecha->diffInDays(Carbon::now());
                $estado = $diasDesdeCreacion > 30 ? 'pagado' : ($diasDesdeCreacion > 15 ? 'aprobado' : 'pendiente');

                // Crear comisión
                $comision = Comision::create([
                    'user_id' => $lider->id,
                    'user_data' => [
                        'name' => $lider->name,
                        'email' => $lider->email,
                        'rol' => $lider->rol
                    ],
                    'pedido_id' => $pedido->id,
                    'pedido_data' => [
                        'numero_pedido' => $numeroPedido,
                        'total' => $totalPedido,
                        'estado' => $pedido->estado,
                        'cliente' => $pedido->cliente_data['name'] ?? 'Cliente',
                        'vendedor' => $pedido->vendedor_data['name'] ?? 'Vendedor'
                    ],
                    'referido_id' => $referido ? $referido->id : null,
                    'referido_data' => $referido ? [
                        'name' => $referido->name,
                        'email' => $referido->email
                    ] : null,
                    'tipo' => $tipo,
                    'porcentaje' => $porcentaje,
                    'monto' => $monto,
                    'estado' => $estado,
                    'fecha_pago' => $estado === 'pagado' ? $fecha->copy()->addDays(rand(7, 14)) : null,
                    'detalles_calculo' => [
                        'base' => $totalPedido,
                        'porcentaje_aplicado' => $porcentaje,
                        'tipo_comision' => $tipo,
                        'fecha_calculo' => $fecha->toDateString()
                    ],
                    'created_at' => $fecha->copy()->addDays(rand(1, 28)),
                    'updated_at' => $fecha->copy()->addDays(rand(1, 28))
                ]);

                $comisionesCreadas++;
                $totalComisiones += $monto;
            }
        }

        // Actualizar datos del líder
        $comisionesPagadas = Comision::where('user_id', $lider->id)
                                     ->where('estado', 'pagado')
                                     ->sum('monto');

        $comisionesDisponibles = Comision::where('user_id', $lider->id)
                                         ->where('estado', 'aprobado')
                                         ->sum('monto');

        // Convertir Decimal128 a float si es necesario
        if ($comisionesPagadas instanceof \MongoDB\BSON\Decimal128) {
            $comisionesPagadas = (float) $comisionesPagadas->__toString();
        }
        if ($comisionesDisponibles instanceof \MongoDB\BSON\Decimal128) {
            $comisionesDisponibles = (float) $comisionesDisponibles->__toString();
        }

        $lider->update([
            'comisiones_ganadas' => $totalComisiones,
            'comisiones_disponibles' => $comisionesDisponibles
        ]);

        // Crear solicitudes de pago de ejemplo
        $this->crearSolicitudesPago($lider, $comisionesPagadas);

        $this->command->info("✅ Seeder completado exitosamente!");
        $this->command->info("📊 Comisiones creadas: {$comisionesCreadas}");
        $this->command->info("💰 Total en comisiones: $" . number_format($totalComisiones, 0, ',', '.'));
        $this->command->info("✅ Comisiones pagadas: $" . number_format($comisionesPagadas, 0, ',', '.'));
        $this->command->info("💵 Comisiones disponibles: $" . number_format($comisionesDisponibles, 0, ',', '.'));
    }

    private function crearSolicitudesPago($lider, $totalPagado)
    {
        // Limpiar solicitudes anteriores
        SolicitudPago::where('user_id', $lider->id)->delete();

        $metodosPago = ['transferencia', 'nequi', 'daviplata', 'efectivo'];
        $estados = ['pagado', 'pendiente', 'aprobado'];

        // Crear entre 3 y 5 solicitudes
        $numSolicitudes = rand(3, 5);

        for ($i = 0; $i < $numSolicitudes; $i++) {
            $estado = $estados[array_rand($estados)];
            $fecha = Carbon::now()->subDays(rand(1, 90));

            SolicitudPago::create([
                'user_id' => $lider->id,
                'user_data' => [
                    'name' => $lider->name,
                    'email' => $lider->email,
                    'rol' => $lider->rol
                ],
                'monto' => rand(100000, 500000),
                'metodo_pago' => $metodosPago[array_rand($metodosPago)],
                'datos_pago' => $this->generarDatosPago($metodosPago[array_rand($metodosPago)]),
                'observaciones' => 'Solicitud de pago ' . ($i + 1),
                'estado' => $estado,
                'fecha_procesado' => $estado === 'pagado' ? $fecha->copy()->addDays(rand(2, 5)) : null,
                'procesado_por' => $estado === 'pagado' ? '1' : null,
                'created_at' => $fecha,
                'updated_at' => $fecha
            ]);
        }

        $this->command->info("💳 Solicitudes de pago creadas: {$numSolicitudes}");
    }

    private function generarDatosPago($metodo)
    {
        switch ($metodo) {
            case 'transferencia':
                return 'Banco: Bancolombia, Cuenta: 1234567890, Tipo: Ahorros';
            case 'nequi':
                return '3001234567';
            case 'daviplata':
                return '3109876543';
            case 'efectivo':
                return 'Oficina principal';
            default:
                return 'Datos de pago';
        }
    }
}
