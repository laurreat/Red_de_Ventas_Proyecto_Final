<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;

class FixPedidosStockDevuelto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedidos:fix-stock-devuelto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige el campo stock_devuelto en pedidos existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando corrección de campo stock_devuelto en pedidos...');

        $pedidos = Pedido::all();
        $actualizados = 0;

        foreach ($pedidos as $pedido) {
            // Si el pedido no tiene el campo stock_devuelto definido
            if (!isset($pedido->stock_devuelto)) {
                // Si el pedido está cancelado, asumimos que el stock ya fue devuelto
                $stockDevuelto = ($pedido->estado === 'cancelado');

                $pedido->update(['stock_devuelto' => $stockDevuelto]);

                $actualizados++;

                $this->line("Pedido {$pedido->numero_pedido}: stock_devuelto = " . ($stockDevuelto ? 'true' : 'false'));
            }
        }

        $this->info("Corrección completada. {$actualizados} pedidos actualizados.");

        return 0;
    }
}
