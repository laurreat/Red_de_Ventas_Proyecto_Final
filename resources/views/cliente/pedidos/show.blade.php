@extends('layouts.app')

@section('title', '- Factura #' . $pedido->numero_pedido)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pedidos-cliente-modern.css') }}?v={{ filemtime(public_path('css/pages/pedidos-cliente-modern.css')) }}">
<style>
/* Estilos de Factura Colombiana */
.factura-container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.factura-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    position: relative;
}

.factura-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ffd700, #ffed4e, #ffd700);
}

.factura-logo {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.factura-tipo {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-top: 10px;
}

.factura-body {
    padding: 40px;
}

.factura-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.factura-section:last-child {
    border-bottom: none;
}

.factura-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #667eea;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.factura-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.factura-info-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.factura-info-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 5px;
}

.factura-info-value {
    font-size: 1.1rem;
    color: #2d3748;
    font-weight: 500;
}

.factura-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 20px;
}

.factura-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.factura-table thead th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 0.95rem;
}

.factura-table thead th:first-child {
    border-top-left-radius: 8px;
}

.factura-table thead th:last-child {
    border-top-right-radius: 8px;
}

.factura-table tbody tr {
    border-bottom: 1px solid #e9ecef;
}

.factura-table tbody tr:hover {
    background: #f8f9fa;
}

.factura-table tbody td {
    padding: 15px;
    vertical-align: middle;
}

.factura-producto-img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e9ecef;
}

.factura-producto-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.factura-producto-nombre {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 5px;
}

.factura-producto-desc {
    font-size: 0.85rem;
    color: #6c757d;
}

.factura-cantidad-badge {
    display: inline-block;
    background: #667eea;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.95rem;
}

.factura-totales {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 8px;
    margin-top: 20px;
}

.factura-total-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    font-size: 1.05rem;
}

.factura-total-row.subtotal {
    border-bottom: 1px solid #dee2e6;
}

.factura-total-row.impuesto {
    border-bottom: 1px solid #dee2e6;
    color: #6c757d;
}

.factura-total-row.total {
    border-top: 3px solid #667eea;
    padding-top: 20px;
    margin-top: 10px;
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
}

.factura-total-row .label {
    font-weight: 600;
}

.factura-footer {
    background: #f8f9fa;
    padding: 30px;
    border-top: 4px solid #667eea;
}

.factura-footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.factura-footer-item {
    text-align: center;
}

.factura-footer-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.factura-footer-value {
    font-weight: 600;
    color: #2d3748;
}

.factura-notas {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 15px;
    border-radius: 4px;
    margin-top: 20px;
}

.factura-qr {
    text-align: center;
    padding: 20px;
}

.factura-legal {
    font-size: 0.75rem;
    color: #6c757d;
    text-align: center;
    padding: 15px;
    border-top: 1px solid #dee2e6;
    margin-top: 20px;
}

/* Estado Badge Mejorado */
.estado-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.95rem;
}

.estado-badge.pendiente {
    background: #fff3cd;
    color: #856404;
    border: 2px solid #ffc107;
}

.estado-badge.confirmado {
    background: #d1ecf1;
    color: #0c5460;
    border: 2px solid #17a2b8;
}

.estado-badge.en_preparacion {
    background: #e2d4f7;
    color: #4a148c;
    border: 2px solid #9c27b0;
}

.estado-badge.enviado {
    background: #cfe2ff;
    color: #084298;
    border: 2px solid #0d6efd;
}

.estado-badge.entregado {
    background: #d1e7dd;
    color: #0f5132;
    border: 2px solid #198754;
}

.estado-badge.cancelado {
    background: #f8d7da;
    color: #842029;
    border: 2px solid #dc3545;
}

/* Botones de Acción */
.factura-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.factura-actions .btn {
    flex: 1;
    min-width: 150px;
}

/* Estilos de Impresión */
@media print {
    body * {
        visibility: hidden;
    }
    
    .factura-container,
    .factura-container * {
        visibility: visible;
    }
    
    .factura-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
    }
    
    .factura-actions,
    .btn,
    nav,
    .navbar,
    .breadcrumb,
    .pedidos-header {
        display: none !important;
    }
    
    .factura-header {
        background: #667eea !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .factura-table thead {
        background: #667eea !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.animate-delay-1 {
    animation-delay: 0.2s;
    animation-fill-mode: backwards;
}

.animate-delay-2 {
    animation-delay: 0.4s;
    animation-fill-mode: backwards;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb de Navegación (No se imprime) -->
    <div class="mb-4 no-print">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('cliente.dashboard') }}">
                        <i class="bi bi-house-door"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('cliente.pedidos.index') }}">Mis Pedidos</a>
                </li>
                <li class="breadcrumb-item active">Factura #{{ $pedido->numero_pedido }}</li>
            </ol>
        </nav>
    </div>

    <!-- Factura Profesional -->
    <div class="factura-container fade-in-up">
        <!-- Encabezado de la Factura -->
        <div class="factura-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="factura-logo">
                        <i class="bi bi-shop"></i>
                        {{ config('app.name', 'Mi Empresa') }}
                    </div>
                    <div>NIT: 900.123.456-7</div>
                    <div>Régimen Común</div>
                    <div class="mt-2">
                        <small>
                            <i class="bi bi-geo-alt me-1"></i>
                            Calle 123 #45-67, Bogotá, Colombia
                        </small>
                    </div>
                    <div>
                        <small>
                            <i class="bi bi-telephone me-1"></i>
                            +57 (1) 234-5678
                        </small>
                    </div>
                    <div>
                        <small>
                            <i class="bi bi-envelope me-1"></i>
                            info@empresa.com
                        </small>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="factura-tipo">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        FACTURA DE VENTA
                    </div>
                    <h2 class="mt-3 mb-2">#{{ $pedido->numero_pedido }}</h2>
                    <div>
                        @php
                            $estadosConfig = [
                                'pendiente' => ['icon' => 'clock-history', 'class' => 'pendiente'],
                                'confirmado' => ['icon' => 'check-circle', 'class' => 'confirmado'],
                                'en_preparacion' => ['icon' => 'hourglass-split', 'class' => 'en_preparacion'],
                                'enviado' => ['icon' => 'truck', 'class' => 'enviado'],
                                'entregado' => ['icon' => 'check-circle-fill', 'class' => 'entregado'],
                                'cancelado' => ['icon' => 'x-circle', 'class' => 'cancelado'],
                            ];
                            $estadoActual = $estadosConfig[$pedido->estado] ?? ['icon' => 'circle', 'class' => 'pendiente'];
                        @endphp
                        <span class="estado-badge {{ $estadoActual['class'] }}">
                            <i class="bi bi-{{ $estadoActual['icon'] }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cuerpo de la Factura -->
        <div class="factura-body">
            <!-- Información Principal -->
            <div class="factura-section">
                <div class="factura-info-grid">
                    <div class="factura-info-item">
                        <div class="factura-info-label">
                            <i class="bi bi-calendar3 me-1"></i>
                            Fecha de Emisión
                        </div>
                        <div class="factura-info-value">
                            {{ $pedido->created_at->format('d/m/Y') }}
                        </div>
                        <small class="text-muted">{{ $pedido->created_at->format('H:i:s') }}</small>
                    </div>
                    
                    @if($pedido->fecha_entrega_estimada)
                    <div class="factura-info-item">
                        <div class="factura-info-label">
                            <i class="bi bi-calendar-check me-1"></i>
                            Fecha de Entrega Estimada
                        </div>
                        <div class="factura-info-value">
                            {{ \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('d/m/Y') }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="factura-info-item">
                        <div class="factura-info-label">
                            <i class="bi bi-credit-card me-1"></i>
                            Método de Pago
                        </div>
                        <div class="factura-info-value">
                            @php
                                $metodosConfig = [
                                    'efectivo' => ['icon' => 'cash-stack', 'label' => '💵 Efectivo'],
                                    'transferencia' => ['icon' => 'bank', 'label' => '🏦 Transferencia'],
                                    'tarjeta' => ['icon' => 'credit-card', 'label' => '💳 Tarjeta'],
                                ];
                                $metodoActual = $metodosConfig[$pedido->metodo_pago] ?? ['label' => 'No especificado'];
                            @endphp
                            {{ $metodoActual['label'] }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos del Cliente -->
            <div class="factura-section">
                <div class="factura-section-title">
                    <i class="bi bi-person-circle"></i>
                    Datos del Cliente
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="factura-info-label">Nombre Completo</div>
                            <div class="factura-info-value">
                                {{ $pedido->cliente_data['name'] ?? auth()->user()->name }}
                                {{ $pedido->cliente_data['apellidos'] ?? '' }}
                            </div>
                        </div>
                        @if(!empty($pedido->cliente_data['cedula']))
                        <div class="mb-3">
                            <div class="factura-info-label">Cédula / NIT</div>
                            <div class="factura-info-value">
                                {{ $pedido->cliente_data['cedula'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="factura-info-label">Correo Electrónico</div>
                            <div class="factura-info-value">
                                {{ $pedido->cliente_data['email'] ?? auth()->user()->email }}
                            </div>
                        </div>
                        @if(!empty($pedido->cliente_data['telefono']))
                        <div class="mb-3">
                            <div class="factura-info-label">Teléfono</div>
                            <div class="factura-info-value">
                                {{ $pedido->cliente_data['telefono'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dirección de Entrega -->
            <div class="factura-section">
                <div class="factura-section-title">
                    <i class="bi bi-truck"></i>
                    Dirección de Entrega
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="factura-info-item">
                            <div class="factura-info-label">
                                <i class="bi bi-geo-alt me-1"></i>
                                Dirección
                            </div>
                            <div class="factura-info-value">
                                {{ $pedido->direccion_entrega }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="factura-info-item">
                            <div class="factura-info-label">
                                <i class="bi bi-telephone me-1"></i>
                                Teléfono de Contacto
                            </div>
                            <div class="factura-info-value">
                                {{ $pedido->telefono_entrega }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle de Productos -->
            <div class="factura-section">
                <div class="factura-section-title">
                    <i class="bi bi-box-seam"></i>
                    Detalle de Productos
                </div>
                
                {{-- Debugging temporal - REMOVER EN PRODUCCIÓN --}}
                @if(config('app.debug'))
                <div class="alert alert-info small mb-3">
                    <strong>DEBUG:</strong>
                    <br>Tipo de detalles: {{ gettype($pedido->detalles) }}
                    <br>Cantidad de detalles: {{ is_array($pedido->detalles) ? count($pedido->detalles) : (is_object($pedido->detalles) ? $pedido->detalles->count() : 'N/A') }}
                    @if(!empty($pedido->detalles) && (is_array($pedido->detalles) || is_object($pedido->detalles)))
                        <br>Primer detalle: <pre class="small">{{ print_r(is_array($pedido->detalles) ? ($pedido->detalles[0] ?? 'vacío') : $pedido->detalles->first(), true) }}</pre>
                    @endif
                </div>
                @endif
                
                @if(empty($pedido->detalles) || count($pedido->detalles) == 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        No hay productos en este pedido
                    </div>
                @else
                <table class="factura-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Item</th>
                            <th style="width: 40%;">Producto</th>
                            <th style="width: 10%;" class="text-center">Cantidad</th>
                            <th style="width: 15%;" class="text-end">Precio Unit.</th>
                            <th style="width: 10%;" class="text-center">IVA</th>
                            <th style="width: 15%;" class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $totalIVA = 0;
                            $detalles = is_array($pedido->detalles) ? $pedido->detalles : $pedido->detalles->toArray();
                        @endphp
                        @foreach($detalles as $index => $detalle)
                        @php
                            // Convertir a array si es necesario
                            $detalleArray = is_array($detalle) ? $detalle : (is_object($detalle) ? (array)$detalle : []);
                            
                            // Obtener valores con seguridad
                            $productoData = $detalleArray['producto_data'] ?? [];
                            $cantidad = $detalleArray['cantidad'] ?? 0;
                            $precioUnitario = $detalleArray['precio_unitario'] ?? 0;
                            $subtotal = $detalleArray['subtotal'] ?? ($cantidad * $precioUnitario);
                            
                            // Calcular IVA (19% en Colombia)
                            // Si el precio ya incluye IVA
                            $baseImponible = $subtotal / 1.19;
                            $iva = $subtotal - $baseImponible;
                            $totalIVA += $iva;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="factura-producto-info">
                                    @php
                                        $imagen = null;
                                        if(is_array($productoData)) {
                                            $imagen = $productoData['imagen'] ?? $productoData['imagen_principal'] ?? null;
                                        } elseif(is_object($productoData)) {
                                            $imagen = $productoData->imagen ?? $productoData->imagen_principal ?? null;
                                        }
                                    @endphp
                                    
                                    @if(!empty($imagen))
                                    <img src="{{ asset('storage/' . $imagen) }}" 
                                         alt="{{ is_array($productoData) ? ($productoData['nombre'] ?? 'Producto') : ($productoData->nombre ?? 'Producto') }}"
                                         class="factura-producto-img"
                                         onerror="this.onerror=null; this.src='{{ asset('images/producto-default.jpg') }}';">
                                    @else
                                    <div class="factura-producto-img d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                    @endif
                                    
                                    <div>
                                        <div class="factura-producto-nombre">
                                            {{ is_array($productoData) ? ($productoData['nombre'] ?? 'Producto') : ($productoData->nombre ?? 'Producto') }}
                                        </div>
                                        @php
                                            $descripcion = is_array($productoData) ? ($productoData['descripcion'] ?? null) : ($productoData->descripcion ?? null);
                                        @endphp
                                        @if(!empty($descripcion))
                                        <div class="factura-producto-desc">
                                            {{ Str::limit($descripcion, 60) }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="factura-cantidad-badge">
                                    {{ $cantidad }}
                                </span>
                            </td>
                            <td class="text-end">
                                ${{ number_format($precioUnitario, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <small class="text-muted">19%</small><br>
                                ${{ number_format($iva, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold">
                                ${{ number_format($subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Totales -->
                <div class="factura-totales">
                    @php
                        $subtotalSinIVA = $pedido->total / 1.19;
                    @endphp
                    
                    <div class="factura-total-row subtotal">
                        <div class="label">Subtotal (Base Imponible):</div>
                        <div>${{ number_format($subtotalSinIVA, 0, ',', '.') }}</div>
                    </div>
                    
                    <div class="factura-total-row impuesto">
                        <div class="label">IVA (19%):</div>
                        <div>${{ number_format($totalIVA, 0, ',', '.') }}</div>
                    </div>
                    
                    @if($pedido->descuento > 0)
                    <div class="factura-total-row impuesto">
                        <div class="label text-success">
                            <i class="bi bi-tag-fill me-1"></i>
                            Descuento:
                        </div>
                        <div class="text-success">-${{ number_format($pedido->descuento, 0, ',', '.') }}</div>
                    </div>
                    @endif
                    
                    <div class="factura-total-row total">
                        <div class="label">
                            <i class="bi bi-cash-coin me-2"></i>
                            TOTAL A PAGAR:
                        </div>
                        <div>${{ number_format($pedido->total_final, 0, ',', '.') }} COP</div>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Son: {{ convertirNumeroALetras($pedido->total_final) }} PESOS M/CTE
                        </small>
                    </div>
                </div>
                @endif
            </div>

            <!-- Notas Adicionales -->
            @if($pedido->notas)
            <div class="factura-section">
                <div class="factura-section-title">
                    <i class="bi bi-chat-left-text"></i>
                    Notas / Observaciones
                </div>
                <div class="factura-notas">
                    <i class="bi bi-sticky-fill me-2"></i>
                    {{ $pedido->notas }}
                </div>
            </div>
            @endif

            <!-- Información Legal y Footer -->
            <div class="factura-footer">
                <div class="text-center mb-3">
                    <strong>Información Importante</strong>
                </div>
                
                <div class="factura-footer-grid">
                    <div class="factura-footer-item">
                        <div class="factura-footer-label">Resolución DIAN</div>
                        <div class="factura-footer-value">18764047820001</div>
                        <small class="text-muted">Del 2024-01-01 al 2025-12-31</small>
                    </div>
                    
                    <div class="factura-footer-item">
                        <div class="factura-footer-label">Rango Autorizado</div>
                        <div class="factura-footer-value">PED-001 a PED-999999</div>
                    </div>
                    
                    <div class="factura-footer-item">
                        <div class="factura-footer-label">Actividad Económica</div>
                        <div class="factura-footer-value">CIIU 4690</div>
                        <small class="text-muted">Comercio al por mayor</small>
                    </div>
                    
                    <div class="factura-footer-item">
                        <div class="factura-footer-label">Responsabilidades</div>
                        <div class="factura-footer-value">IVA - RETEICA</div>
                    </div>
                </div>

                <!-- QR Code (Placeholder) -->
                <div class="factura-qr mt-4">
                    <div class="d-inline-block p-3 bg-white border rounded">
                        <i class="bi bi-qr-code" style="font-size: 4rem;"></i>
                        <div class="mt-2">
                            <small class="text-muted">Código QR de Verificación</small>
                        </div>
                    </div>
                </div>

                <!-- Texto Legal -->
                <div class="factura-legal">
                    <p class="mb-2">
                        <strong>TÉRMINOS Y CONDICIONES:</strong>
                    </p>
                    <p class="mb-1">
                        Esta factura se asimila en todos sus efectos a una letra de cambio según el Art. 774 del Código de Comercio.
                    </p>
                    <p class="mb-1">
                        Los pagos se deben efectuar en la cuenta bancaria registrada. No se aceptan devoluciones después de 30 días de la compra.
                    </p>
                    <p class="mb-0">
                        Para cualquier aclaración, comuníquese al teléfono +57 (1) 234-5678 o al correo info@empresa.com
                    </p>
                </div>
            </div>

            <!-- Acciones (No se imprimen) -->
            <div class="factura-actions no-print">
                <button type="button" 
                        class="btn btn-primary btn-lg"
                        onclick="window.print()">
                    <i class="bi bi-printer-fill me-2"></i>
                    Imprimir Factura
                </button>
                
                <button type="button" 
                        class="btn btn-success btn-lg"
                        onclick="descargarPDF()">
                    <i class="bi bi-file-pdf-fill me-2"></i>
                    Descargar PDF
                </button>
                
                <button type="button" 
                        class="btn btn-info btn-lg"
                        onclick="compartirFactura()">
                    <i class="bi bi-share-fill me-2"></i>
                    Compartir
                </button>
                
                @if($pedido->puedeSerCancelado())
                <button type="button" 
                        class="btn btn-danger btn-lg"
                        onclick="cancelarPedido('{{ $pedido->_id }}')">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    Cancelar Pedido
                </button>
                @endif
                
                <a href="{{ route('cliente.pedidos.index') }}" 
                   class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left me-2"></i>
                    Volver a Mis Pedidos
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="pedidos-loading-overlay" style="display: none;">
    <div class="pedidos-loading-spinner"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/pedidos-cliente-modern.js') }}?v={{ filemtime(public_path('js/pages/pedidos-cliente-modern.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof PedidosClienteManager !== 'undefined') {
        window.pedidosManager = new PedidosClienteManager();
    }
    
    @if(session('success'))
        if (window.pedidosManager) {
            pedidosManager.showToast('success', 'Éxito', '{{ session('success') }}');
        }
    @endif
    
    @if(session('error'))
        if (window.pedidosManager) {
            pedidosManager.showToast('error', 'Error', '{{ session('error') }}');
        }
    @endif
});

/**
 * Función para descargar PDF
 */
function descargarPDF() {
    if (window.pedidosManager) {
        pedidosManager.showToast('info', 'Próximamente', 'La descarga de PDF estará disponible pronto');
    } else {
        alert('La descarga de PDF estará disponible pronto');
    }
}

/**
 * Función para compartir factura
 */
function compartirFactura() {
    const url = window.location.href;
    const titulo = 'Factura #{{ $pedido->numero_pedido }}';
    
    if (navigator.share) {
        navigator.share({
            title: titulo,
            text: 'Ver mi factura de pedido',
            url: url
        }).then(() => {
            if (window.pedidosManager) {
                pedidosManager.showToast('success', 'Compartido', 'Factura compartida exitosamente');
            }
        }).catch(err => {
            console.log('Error al compartir:', err);
        });
    } else {
        // Copiar al portapapeles
        navigator.clipboard.writeText(url).then(() => {
            if (window.pedidosManager) {
                pedidosManager.showToast('success', 'Copiado', 'Enlace copiado al portapapeles');
            } else {
                alert('Enlace copiado al portapapeles');
            }
        }).catch(err => {
            console.error('Error al copiar:', err);
        });
    }
}

/**
 * Función para cancelar pedido
 */
function cancelarPedido(pedidoId) {
    if (!confirm('¿Estás seguro de que deseas cancelar este pedido?')) {
        return;
    }
    
    if (window.pedidosManager) {
        pedidosManager.showLoading('Cancelando pedido...');
    }
    
    fetch(`/cliente/pedidos/${pedidoId}/cancelar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            motivo: 'Cancelado por el cliente'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (window.pedidosManager) {
            pedidosManager.hideLoading();
        }
        
        if (data.success) {
            if (window.pedidosManager) {
                pedidosManager.showToast('success', 'Pedido cancelado', 'El pedido ha sido cancelado exitosamente');
            } else {
                alert('Pedido cancelado exitosamente');
            }
            setTimeout(() => location.reload(), 1500);
        } else {
            if (window.pedidosManager) {
                pedidosManager.showToast('error', 'Error', data.message || 'No se pudo cancelar el pedido');
            } else {
                alert(data.message || 'No se pudo cancelar el pedido');
            }
        }
    })
    .catch(err => {
        if (window.pedidosManager) {
            pedidosManager.hideLoading();
            pedidosManager.showToast('error', 'Error', 'Ocurrió un error al cancelar el pedido');
        } else {
            alert('Ocurrió un error al cancelar el pedido');
        }
        console.error('Error al cancelar pedido:', err);
    });
}
</script>
@endpush

@php
/**
 * Función helper para convertir números a letras (formato colombiano)
 * 
 * @param float $numero
 * @return string
 */
function convertirNumeroALetras($numero) {
    $numero = intval($numero);
    
    $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
    $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
    $especiales = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
    $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
    
    if ($numero == 0) return 'CERO';
    if ($numero == 100) return 'CIEN';
    
    $resultado = '';
    
    // Millones
    if ($numero >= 1000000) {
        $millones = intval($numero / 1000000);
        if ($millones == 1) {
            $resultado .= 'UN MILLÓN ';
        } else {
            $resultado .= convertirNumeroALetrasHelper($millones, $unidades, $decenas, $especiales, $centenas) . ' MILLONES ';
        }
        $numero %= 1000000;
    }
    
    // Miles
    if ($numero >= 1000) {
        $miles = intval($numero / 1000);
        if ($miles == 1) {
            $resultado .= 'MIL ';
        } else {
            $resultado .= convertirNumeroALetrasHelper($miles, $unidades, $decenas, $especiales, $centenas) . ' MIL ';
        }
        $numero %= 1000;
    }
    
    // Resto
    if ($numero > 0) {
        $resultado .= convertirNumeroALetrasHelper($numero, $unidades, $decenas, $especiales, $centenas);
    }
    
    return trim($resultado);
}

function convertirNumeroALetrasHelper($numero, $unidades, $decenas, $especiales, $centenas) {
    $resultado = '';
    
    // Centenas
    if ($numero >= 100) {
        $centena = intval($numero / 100);
        if ($numero == 100) {
            return 'CIEN';
        }
        $resultado .= $centenas[$centena] . ' ';
        $numero %= 100;
    }
    
    // Decenas y unidades
    if ($numero >= 10) {
        if ($numero >= 10 && $numero <= 19) {
            $resultado .= $especiales[$numero - 10];
            $numero = 0;
        } else {
            $decena = intval($numero / 10);
            $resultado .= $decenas[$decena];
            $numero %= 10;
            if ($numero > 0) {
                $resultado .= ' Y ' . $unidades[$numero];
                $numero = 0;
            }
        }
    }
    
    // Unidades
    if ($numero > 0 && $numero < 10) {
        $resultado .= $unidades[$numero];
    }
    
    return trim($resultado);
}
@endphp
