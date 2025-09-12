<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;">
    @foreach($toasts as $toast)
        <div 
            wire:key="toast-{{ $toast['id'] }}"
            class="toast show mb-2" 
            role="alert" 
            aria-live="assertive" 
            aria-atomic="true"
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-2"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-2"
        >
            <div class="toast-header 
                @switch($toast['type'])
                    @case('success')
                        bg-success text-white
                        @break
                    @case('error')
                        bg-danger text-white
                        @break
                    @case('warning')
                        bg-warning text-dark
                        @break
                    @case('info')
                    @default
                        bg-primary text-white
                @endswitch
            ">
                <i class="bi 
                    @switch($toast['type'])
                        @case('success')
                            bi-check-circle-fill
                            @break
                        @case('error')
                            bi-x-circle-fill
                            @break
                        @case('warning')
                            bi-exclamation-triangle-fill
                            @break
                        @case('info')
                        @default
                            bi-info-circle-fill
                    @endswitch
                    me-2"></i>
                <strong class="me-auto">
                    @switch($toast['type'])
                        @case('success')
                            Éxito
                            @break
                        @case('error')
                            Error
                            @break
                        @case('warning')
                            Advertencia
                            @break
                        @case('info')
                        @default
                            Información
                    @endswitch
                </strong>
                <small>{{ $toast['timestamp']->diffForHumans() }}</small>
                <button 
                    type="button" 
                    class="btn-close btn-close-white ms-2" 
                    wire:click="removeToast('{{ $toast['id'] }}')"
                    aria-label="Close"
                ></button>
            </div>
            <div class="toast-body">
                {{ $toast['message'] }}
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
    // Auto-hide toasts after specified duration
    document.addEventListener('livewire:init', () => {
        Livewire.on('hideToastAfter', (toastId, duration) => {
            setTimeout(() => {
                Livewire.dispatch('hideToast', { toastId: toastId });
            }, duration);
        });
    });
    
    // Global function to show toasts
    window.showToast = function(message, type = 'info', duration = 4000) {
        Livewire.dispatch('showToast', { 
            message: message, 
            type: type, 
            duration: duration 
        });
    };
</script>
@endpush
