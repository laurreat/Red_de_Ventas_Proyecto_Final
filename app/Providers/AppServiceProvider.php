<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Pedido;
use App\Observers\PedidoObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar el Observer para Pedido
        Pedido::observe(PedidoObserver::class);
    }
}
