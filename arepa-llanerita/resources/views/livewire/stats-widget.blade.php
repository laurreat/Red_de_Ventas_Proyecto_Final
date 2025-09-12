<div 
    class="card metric-card h-100" 
    wire:poll.{{ $refreshInterval }}ms="loadValue"
    x-data="{ 
        animateValue: false,
        oldValue: @entangle('value'),
        initValue: @entangle('value')
    }"
    x-init="
        $watch('$wire.value', (value, oldValue) => {
            if (oldValue !== undefined && value !== oldValue) {
                animateValue = true;
                setTimeout(() => animateValue = false, 600);
            }
        })
    "
>
    <div class="card-body text-center">
        <i class="bi {{ $icon }} text-{{ $color }} fs-1 mb-3"
           :class="{ 'animate-pulse': animateValue }"></i>
        
        <div class="metric-value text-{{ $color }}"
             :class="{ 'animate-bounce': animateValue }"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-50 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            @if(in_array($type, ['ventas_mes', 'ventas_hoy']))
                ${{ number_format($value, 0) }}
            @else
                {{ number_format($value) }}
            @endif
        </div>
        
        <div class="metric-label">{{ $title }}</div>
        
        @if($value > 0)
            <small class="text-muted">
                <i class="bi bi-arrow-clockwise me-1"></i>
                Actualizado automáticamente
            </small>
        @endif
    </div>
    
    <!-- Loading indicator when polling -->
    <div class="position-absolute top-0 end-0 p-2" wire:loading.delay>
        <div class="spinner-border spinner-border-sm text-{{ $color }}" role="status">
            <span class="visually-hidden">Actualizando...</span>
        </div>
    </div>
</div>

@push('styles')
<style>
    .metric-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .animate-pulse {
        animation: pulse 0.6s ease-in-out;
    }
    
    .animate-bounce {
        animation: bounce 0.6s ease-in-out;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-5px); }
        60% { transform: translateY(-3px); }
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    
    .metric-label {
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
</style>
@endpush
