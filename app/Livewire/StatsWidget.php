<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Producto;

class StatsWidget extends Component
{
    public $title;
    public $value;
    public $icon;
    public $color;
    public $type;
    public $refreshInterval = 30000; // 30 segundos
    
    public function mount($title, $type, $icon = 'bi-info-circle', $color = 'primary', $refreshInterval = 30000)
    {
        $this->title = $title;
        $this->type = $type;
        $this->icon = $icon;
        $this->color = $color;
        $this->refreshInterval = $refreshInterval;
        
        $this->loadValue();
    }
    
    public function loadValue()
    {
        switch ($this->type) {
            case 'total_usuarios':
                $this->value = User::count();
                break;
                
            case 'usuarios_activos':
                $this->value = User::where('activo', true)->count();
                break;
                
            case 'total_vendedores':
                $this->value = User::whereIn('rol', ['vendedor', 'lider'])->where('activo', true)->count();
                break;
                
            case 'pedidos_hoy':
                $this->value = Pedido::whereDate('created_at', today())->count();
                break;
                
            case 'pedidos_mes':
                $this->value = Pedido::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->count();
                break;
                
            case 'ventas_mes':
                $this->value = Pedido::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->sum('total');
                break;
                
            case 'ventas_hoy':
                $this->value = Pedido::whereDate('created_at', today())->sum('total');
                break;
                
            case 'productos_total':
                $this->value = Producto::count();
                break;
                
            case 'productos_activos':
                $this->value = Producto::where('activo', true)->count();
                break;
                
            case 'stock_bajo':
                $this->value = Producto::where('stock', '<=', 10)->count();
                break;
                
            default:
                $this->value = 0;
        }
    }
    
    #[On('refresh-stats')]
    public function refreshStats()
    {
        $this->loadValue();
    }
    
    public function render()
    {
        return view('livewire.stats-widget');
    }
}
