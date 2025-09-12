<?php

namespace App\Livewire;

use Livewire\Component;

class ToastNotifications extends Component
{
    public $toasts = [];
    
    protected $listeners = [
        'showToast' => 'addToast',
        'hideToast' => 'removeToast'
    ];
    
    public function addToast($message, $type = 'info', $duration = 4000)
    {
        $toastId = uniqid();
        
        $this->toasts[] = [
            'id' => $toastId,
            'message' => $message,
            'type' => $type,
            'timestamp' => now()
        ];
        
        // Auto-hide toast after duration
        $this->dispatch('hideToastAfter', $toastId, $duration);
    }
    
    public function removeToast($toastId)
    {
        $this->toasts = array_filter($this->toasts, function($toast) use ($toastId) {
            return $toast['id'] !== $toastId;
        });
    }
    
    public function render()
    {
        return view('livewire.toast-notifications');
    }
}
