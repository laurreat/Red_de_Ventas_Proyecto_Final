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
<link rel="stylesheet" href="{{ asset('css/components/stats-widget.css') }}">
@endpush
