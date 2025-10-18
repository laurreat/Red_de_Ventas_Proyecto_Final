<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;">
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $toasts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $toast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div 
            wire:key="toast-<?php echo e($toast['id']); ?>"
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
                <?php switch($toast['type']):
                    case ('success'): ?>
                        bg-success color: var(--color-white)  #fff = #ffffff ;
                        <?php break; ?>
                    <?php case ('error'): ?>
                        bg-danger color: var(--color-white)  #fff = #ffffff ;
                        <?php break; ?>
                    <?php case ('warning'): ?>
                        bg-warning text-dark
                        <?php break; ?>
                    <?php case ('info'): ?>
                    <?php default: ?>
                        bg-primary text-white
                <?php endswitch; ?>
            ">
                <i class="bi 
                    <?php switch($toast['type']):
                        case ('success'): ?>
                            bi-check-circle-fill
                            <?php break; ?>
                        <?php case ('error'): ?>
                            bi-x-circle-fill
                            <?php break; ?>
                        <?php case ('warning'): ?>
                            bi-exclamation-triangle-fill
                            <?php break; ?>
                        <?php case ('info'): ?>
                        <?php default: ?>
                            bi-info-circle-fill
                    <?php endswitch; ?>
                    me-2"></i>
                <strong class="me-auto">
                    <!--[if BLOCK]><![endif]--><?php switch($toast['type']):
                        case ('success'): ?>
                            Éxito
                            <?php break; ?>
                        <?php case ('error'): ?>
                            Error
                            <?php break; ?>
                        <?php case ('warning'): ?>
                            Advertencia
                            <?php break; ?>
                        <?php case ('info'): ?>
                        <?php default: ?>
                            Información
                    <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                </strong>
                <small><?php echo e($toast['timestamp']->diffForHumans()); ?></small>
                <button 
                    type="button" 
                    class="btn-close btn-close-white ms-2" 
                    wire:click="removeToast('<?php echo e($toast['id']); ?>')"
                    aria-label="Close"
                ></button>
            </div>
            <div class="toast-body">
                <?php echo e($toast['message']); ?>

            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\Proyecto_Final\Red_de_Ventas_Proyecto_Final\arepa-llanerita\resources\views/livewire/toast-notifications.blade.php ENDPATH**/ ?>