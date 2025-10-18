@extends('layouts.app')

@section('title', '- Detalle del Pedido #' . $pedido->numero_pedido)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pedidos-cliente-modern.css') }}?v={{ filemtime(public_path('css/pages/pedidos-cliente-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header Hero -->
    <div class="pedidos-header fade-in-up">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <a href="{{ route('cliente.pedidos.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h1 class="pedidos-title mb-0">
                        <i class="bi bi-receipt me-2"></i>
                        Pedido #{{ $pedido->numero_pedido }}
                    </h1>
                </div>
                <p class="pedidos-subtitle mb-0">
                    <i class="bi bi-calendar3 me-2"></i>
                    Realizado el {{ $pedido->created_at->format('d/m/Y \a \l\a\s H:i') }}
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                @php
                    $estadosConfig = [
                        'pendiente' => ['class' => 'warning', 'icon' => 'clock-history'],
                        'confirmado' => ['class' => 'info', 'icon' => 'check-circle'],
                        'en_preparacion' => ['class' => 'purple', 'icon' => 'hourglass-split'],
                        'enviado' => ['class' => 'success', 'icon' => 'truck'],
                        'entregado' => ['class' => 'success', 'icon' => 'check-circle-fill'],
                        'cancelado' => ['class' => 'danger', 'icon' => 'x-circle'],
                    ];
                    $estadoActual = $estadosConfig[$pedido->estado] ?? ['class' => 'secondary', 'icon' => 'circle'];
                @endphp
                <span class="pedidos-badge pedidos-badge-{{ $pedido->estado }}" style="font-size: 1.1rem; padding: 0.75rem 1.5rem;">
                    <i class="bi bi-{{ $estadoActual['icon'] }}"></i>
                    {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna Principal -->
        <div class="col-lg-8 mb-4">
            <!-- Productos del Pedido -->
            <div class="pedidos-form-section fade-in-up">
                <h5 class="pedidos-form-section-title">
                    <i class="bi bi-box-seam"></i>
                    Productos Ordenados
                </h5>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->detalles as $detalle)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if(!empty($detalle['producto_data']['imagen']))
                                        <img src="{{ asset('storage/' . $detalle['producto_data']['imagen']) }}" 
                                             alt="{{ $detalle['producto_data']['nombre'] }}"
                                             class="pedidos-product-image"
                                             onerror="this.onerror=null; this.src='{{ asset('images/producto-default.jpg') }}';">
                                        @else
                                        <div class="pedidos-product-placeholder">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $detalle['producto_data']['nombre'] }}</div>
                                            @if(!empty($detalle['producto_data']['descripcion']))
                                            <small class="text-muted">{{ Str::limit($detalle['producto_data']['descripcion'], 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $detalle['cantidad'] }}</span>
                                </td>
                                <td class="text-end">
                                    ${{ number_format($detalle['precio_unitario'], 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold">
                                    ${{ number_format($detalle['subtotal'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-top-2">
                                <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                <td class="text-end fw-bold">${{ number_format($pedido->total, 0, ',', '.') }}</td>
                            </tr>
                            @if($pedido->descuento > 0)
                            <tr>
                                <td colspan="3" class="text-end text-success">Descuento:</td>
                                <td class="text-end text-success">-${{ number_format($pedido->descuento, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr class="table-primary">
                                <td colspan="3" class="text-end fw-bold fs-5">TOTAL:</td>
                                <td class="text-end fw-bold text-success fs-4">
                                    ${{ number_format($pedido->total_final, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Timeline de Estados -->
            <div class="pedidos-form-section fade-in-up animate-delay-1">
                <h5 class="pedidos-form-section-title">
                    <i class="bi bi-clock-history"></i>
                    Historial del Pedido
                </h5>
                
                <div class="pedidos-timeline">
                    @if(!empty($pedido->historial_estados))
                        @foreach($pedido->historial_estados as $index => $historial)
                        <div class="pedidos-timeline-item">
                            <div class="pedidos-timeline-dot {{ $index === count($pedido->historial_estados) - 1 ? 'current' : 'completed' }}"></div>
                            <div class="pedidos-timeline-content">
                                <div class="pedidos-timeline-title">
                                    {{ ucfirst(str_replace('_', ' ', $historial['estado_nuevo'] ?? $historial['estado'])) }}
                                </div>
                                <div class="pedidos-timeline-date">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($historial['fecha'])->format('d/m/Y H:i') }}
                                </div>
                                @if(!empty($historial['motivo']))
                                <div class="pedidos-timeline-description">
                                    {{ $historial['motivo'] }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-info-circle me-2"></i>
                            No hay historial disponible
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Información de Entrega -->
            <div class="pedidos-form-section fade-in-up animate-delay-2">
                <h5 class="pedidos-form-section-title">
                    <i class="bi bi-truck"></i>
                    Información de Entrega
                </h5>
                
                <div class="mb-3">
                    <label class="text-muted small">Dirección</label>
                    <div class="fw-semibold">
                        <i class="bi bi-geo-alt text-danger me-2"></i>
                        {{ $pedido->direccion_entrega }}
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Teléfono de contacto</label>
                    <div class="fw-semibold">
                        <i class="bi bi-telephone text-info me-2"></i>
                        {{ $pedido->telefono_entrega }}
                    </div>
                </div>
                
                @if($pedido->fecha_entrega_estimada)
                <div class="mb-3">
                    <label class="text-muted small">Fecha estimada de entrega</label>
                    <div class="fw-semibold">
                        <i class="bi bi-calendar-check text-success me-2"></i>
                        {{ \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('d/m/Y') }}
                    </div>
                </div>
                @endif
                
                @if($pedido->notas)
                <div class="mb-3">
                    <label class="text-muted small">Notas adicionales</label>
                    <div class="bg-light p-3 rounded">
                        <i class="bi bi-chat-left-text me-2"></i>
                        {{ $pedido->notas }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Información del Cliente -->
            <div class="pedidos-form-section fade-in-up animate-delay-3">
                <h5 class="pedidos-form-section-title">
                    <i class="bi bi-person-circle"></i>
                    Tus Datos
                </h5>
                
                <div class="mb-3">
                    <label class="text-muted small">Nombre</label>
                    <div class="fw-semibold">
                        {{ $pedido->cliente_data['name'] ?? auth()->user()->name }}
                        {{ $pedido->cliente_data['apellidos'] ?? '' }}
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Email</label>
                    <div class="fw-semibold">
                        <i class="bi bi-envelope me-2"></i>
                        {{ $pedido->cliente_data['email'] ?? auth()->user()->email }}
                    </div>
                </div>
                
                @if(!empty($pedido->cliente_data['telefono']))
                <div class="mb-3">
                    <label class="text-muted small">Teléfono</label>
                    <div class="fw-semibold">
                        <i class="bi bi-phone me-2"></i>
                        {{ $pedido->cliente_data['telefono'] }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Método de Pago -->
            <div class="pedidos-form-section fade-in-up animate-delay-3">
                <h5 class="pedidos-form-section-title">
                    <i class="bi bi-credit-card"></i>
                    Método de Pago
                </h5>
                
                @php
                    $metodosConfig = [
                        'efectivo' => ['icon' => 'cash-stack', 'label' => 'Efectivo', 'class' => 'success'],
                        'transferencia' => ['icon' => 'bank', 'label' => 'Transferencia', 'class' => 'info'],
                        'tarjeta' => ['icon' => 'credit-card', 'label' => 'Tarjeta', 'class' => 'primary'],
                    ];
                    $metodoActual = $metodosConfig[$pedido->metodo_pago] ?? ['icon' => 'cash', 'label' => 'No especificado', 'class' => 'secondary'];
                @endphp
                
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                    <div class="bg-{{ $metodoActual['class'] }} bg-opacity-10 text-{{ $metodoActual['class'] }} rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-{{ $metodoActual['icon'] }} fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $metodoActual['label'] }}</div>
                        <small class="text-muted">Método seleccionado</small>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="pedidos-form-section fade-in-up animate-delay-3">
                <h5 class="pedidos-form-section-title">
                    <i class="bi bi-gear"></i>
                    Acciones
                </h5>
                
                <div class="d-grid gap-2">
                    @if($pedido->puedeSerCancelado())
                    <button type="button" 
                            class="btn btn-danger"
                            onclick="pedidosManager.showCancelModal('{{ $pedido->_id }}', '{{ $pedido->numero_pedido }}')">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancelar Pedido
                    </button>
                    @endif
                    
                    <button type="button" 
                            class="btn btn-outline-primary"
                            onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>
                        Imprimir
                    </button>
                    
                    <a href="{{ route('cliente.pedidos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver a Mis Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="pedidos-loading-overlay">
    <div class="pedidos-loading-spinner"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/pedidos-cliente-modern.js') }}?v={{ filemtime(public_path('js/pages/pedidos-cliente-modern.js')) }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.pedidosManager = new PedidosClienteManager();
        
        @if(session('success'))
            pedidosManager.showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            pedidosManager.showToast('{{ session('error') }}', 'error');
        @endif
    });
</script>
@endpush
