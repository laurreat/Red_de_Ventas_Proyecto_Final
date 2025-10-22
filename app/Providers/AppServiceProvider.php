<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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
        
        // Usar la vista de paginación personalizada por defecto
        Paginator::defaultView('pagination::custom');
        Paginator::defaultSimpleView('pagination::simple-default');
    }
}
