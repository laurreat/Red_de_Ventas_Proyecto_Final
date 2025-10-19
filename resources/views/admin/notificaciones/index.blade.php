@extends('layouts.admin')

@section('title', 'Notificaciones')

@push('styles')
    <link href="{{ asset('css/admin/notificaciones-modern.css') }}?v={{ time() }}" rel="stylesheet">
    <style>
        /* ========== GLASSMORPHISM MODALS ========== */
        .glass-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-modal-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .glass-modal {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 
                0 25px 70px rgba(0, 0, 0, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.6) inset;
            max-width: 550px;
            width: 92%;
            max-height: 92vh;
            overflow: hidden;
            transform: scale(0.88) translateY(30px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .glass-modal-backdrop.active .glass-modal {
            transform: scale(1) translateY(0);
        }

        .glass-modal-header {
            position: relative;
            padding: 2.5rem 2rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.2rem;
            overflow: hidden;
        }

        .glass-modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(114, 47, 55, 0.15) 0%, 
                rgba(114, 47, 55, 0.08) 100%);
            z-index: 0;
        }

        .glass-modal-header.success::before {
            background: linear-gradient(135deg, 
                rgba(16, 185, 129, 0.15) 0%, 
                rgba(16, 185, 129, 0.08) 100%);
        }

        .glass-modal-header.danger::before {
            background: linear-gradient(135deg, 
                rgba(239, 68, 68, 0.15) 0%, 
                rgba(239, 68, 68, 0.08) 100%);
        }

        .glass-modal-header.info::before {
            background: linear-gradient(135deg, 
                rgba(59, 130, 246, 0.15) 0%, 
                rgba(59, 130, 246, 0.08) 100%);
        }

        .glass-modal-icon {
            position: relative;
            z-index: 1;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            box-shadow: 
                0 10px 40px rgba(0, 0, 0, 0.15),
                inset 0 0 0 1px rgba(255, 255, 255, 0.5);
            animation: iconPulse 3s ease-in-out infinite;
        }

        .glass-modal-icon.success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .glass-modal-icon.danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .glass-modal-icon.info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        @keyframes iconPulse {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
            }
        }

        .glass-modal-title {
            position: relative;
            z-index: 1;
            font-size: 1.9rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
            text-align: center;
            letter-spacing: -0.03em;
        }

        .glass-modal-close {
            position: absolute;
            top: 1.2rem;
            right: 1.2rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #6b7280;
            font-size: 1.3rem;
            z-index: 2;
        }

        .glass-modal-close:hover {
            background: rgba(255, 255, 255, 1);
            color: #111827;
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .glass-modal-body {
            padding: 2rem;
            position: relative;
            overflow-y: auto;
            max-height: calc(92vh - 320px);
        }

        .glass-modal-content {
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
        }

        .glass-modal-text {
            font-size: 1.15rem;
            color: #4b5563;
            text-align: center;
            margin: 0;
            line-height: 1.7;
            font-weight: 500;
        }

        .glass-info-card {
            background: linear-gradient(135deg, 
                rgba(114, 47, 55, 0.06) 0%, 
                rgba(114, 47, 55, 0.02) 100%);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 1.75rem;
            border: 1px solid rgba(114, 47, 55, 0.12);
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .glass-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.9rem;
            border-bottom: 1px solid rgba(114, 47, 55, 0.12);
        }

        .glass-info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .glass-info-label {
            font-size: 0.95rem;
            color: #6b7280;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .glass-info-value {
            font-size: 1.15rem;
            color: #111827;
            font-weight: 800;
        }

        .glass-form-group {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .glass-form-label {
            font-size: 0.95rem;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .glass-form-input,
        .glass-form-textarea {
            width: 100%;
            padding: 0.95rem 1.2rem;
            border: 2px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            color: #111827;
        }

        .glass-form-input:focus,
        .glass-form-textarea:focus {
            outline: none;
            border-color: #722f37;
            box-shadow: 0 0 0 4px rgba(114, 47, 55, 0.1);
            background: rgba(255, 255, 255, 1);
        }

        .glass-form-textarea {
            min-height: 110px;
            resize: vertical;
            font-family: inherit;
        }

        .glass-modal-note {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
            padding: 1.2rem 1.5rem;
            border-radius: 14px;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        .glass-modal-note.warning {
            background: rgba(239, 68, 68, 0.08);
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .glass-modal-note.info {
            background: rgba(59, 130, 246, 0.08);
            border-left: 4px solid #3b82f6;
            color: #1e40af;
        }

        .glass-modal-note i {
            font-size: 1.3rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .glass-modal-footer {
            padding: 1.75rem 2rem;
            background: rgba(249, 250, 251, 0.9);
            backdrop-filter: blur(15px);
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .glass-btn {
            padding: 1rem 2rem;
            border-radius: 14px;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
        }

        .glass-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .glass-btn:active::before {
            width: 350px;
            height: 350px;
        }

        .glass-btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.35);
        }

        .glass-btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45);
        }

        .glass-btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.35);
        }

        .glass-btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.45);
        }

        .glass-btn-info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.35);
        }

        .glass-btn-info:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.45);
        }

        .glass-btn-secondary {
            background: rgba(107, 114, 128, 0.12);
            backdrop-filter: blur(10px);
            color: #4b5563;
            border: 1.5px solid rgba(107, 114, 128, 0.25);
        }

        .glass-btn-secondary:hover {
            background: rgba(107, 114, 128, 0.22);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
            border-color: rgba(107, 114, 128, 0.35);
        }

        /* Animaciones de entrada escalonada */
        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-modal-body > * {
            animation: slideUpFade 0.5s ease-out backwards;
        }

        .glass-modal-body > *:nth-child(1) { animation-delay: 0.1s; }
        .glass-modal-body > *:nth-child(2) { animation-delay: 0.2s; }
        .glass-modal-body > *:nth-child(3) { animation-delay: 0.3s; }
        .glass-modal-body > *:nth-child(4) { animation-delay: 0.4s; }

        /* Responsive */
        @media (max-width: 768px) {
            .glass-modal {
                width: 96%;
                border-radius: 22px;
            }
            
            .glass-modal-header {
                padding: 2rem 1.5rem 1.5rem;
            }
            
            .glass-modal-icon {
                width: 70px;
                height: 70px;
                font-size: 2.2rem;
            }
            
            .glass-modal-title {
                font-size: 1.5rem;
            }
            
            .glass-modal-body {
                padding: 1.5rem;
            }
            
            .glass-modal-footer {
                flex-direction: column-reverse;
                padding: 1.5rem;
            }
            
            .glass-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Scrollbar personalizado */
        .glass-modal-body::-webkit-scrollbar {
            width: 9px;
        }

        .glass-modal-body::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.04);
            border-radius: 10px;
        }

        .glass-modal-body::-webkit-scrollbar-thumb {
            background: rgba(114, 47, 55, 0.35);
            border-radius: 10px;
        }

        .glass-modal-body::-webkit-scrollbar-thumb:hover {
            background: rgba(114, 47, 55, 0.55);
        }

        /* Animaciones para actualización de estado */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }

        .fade-in-animation {
            animation: slideInUp 0.5s ease-out;
        }

        /* Estilos para badges de estado procesado */
        .alert-heading {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert code {
            background: rgba(0, 0, 0, 0.05);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9em;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Header -->
    <div class="notif-header fade-in-up">
        <div class="notif-header-content">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <h1 class="notif-title">
                        <i class="bi bi-bell-fill me-2"></i>
                        Centro de Notificaciones
                    </h1>
                    <p class="notif-subtitle mb-0">
                        Gestiona todas tus notificaciones del sistema
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="notif-actions justify-content-lg-end">
                        <button class="notif-btn notif-btn-outline" onclick="crearNotificacionesPrueba()">
                            <i class="bi bi-plus-circle"></i>
                            Crear Pruebas
                        </button>
                        <button class="notif-btn notif-btn-white" onclick="marcarTodasLeidas()">
                            <i class="bi bi-check-all"></i>
                            Marcar Todas
                        </button>
                        <button class="notif-btn notif-btn-outline" onclick="limpiarLeidas()">
                            <i class="bi bi-trash"></i>
                            Limpiar Leídas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="notif-stat-card scale-in animate-delay-1">
                <div class="notif-stat-icon" style="background: rgba(59,130,246,0.1); color: var(--info);">
                    <i class="bi bi-bell"></i>
                </div>
                <h2 class="notif-stat-value">{{ $stats['total'] }}</h2>
                <p class="notif-stat-label">Total Notificaciones</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="notif-stat-card scale-in animate-delay-2">
                <div class="notif-stat-icon" style="background: rgba(245,158,11,0.1); color: var(--warning);">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <h2 class="notif-stat-value">{{ $stats['no_leidas'] }}</h2>
                <p class="notif-stat-label">Sin Leer</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="notif-stat-card scale-in animate-delay-3">
                <div class="notif-stat-icon" style="background: rgba(16,185,129,0.1); color: var(--success);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h2 class="notif-stat-value">{{ $stats['leidas'] }}</h2>
                <p class="notif-stat-label">Leídas</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="notif-filter-card fade-in">
        <div class="notif-filter-body">
            <form method="GET" action="{{ route('admin.notificaciones.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="notif-form-label">
                            <i class="bi bi-funnel me-1"></i>
                            Estado
                        </label>
                        <select name="filter" class="notif-form-control">
                            <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Todas</option>
                            <option value="unread" {{ $filter == 'unread' ? 'selected' : '' }}>Sin Leer</option>
                            <option value="read" {{ $filter == 'read' ? 'selected' : '' }}>Leídas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="notif-form-label">
                            <i class="bi bi-tag me-1"></i>
                            Tipo
                        </label>
                        <select name="tipo" class="notif-form-control">
                            <option value="">Todos los tipos</option>
                            @foreach($tipos as $tipoItem)
                            <option value="{{ $tipoItem }}" {{ $tipo == $tipoItem ? 'selected' : '' }}>
                                {{ ucfirst($tipoItem) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="notif-form-label">&nbsp;</label>
                        <button type="submit" class="notif-btn notif-btn-white" style="width: 100%;">
                            <i class="bi bi-search"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Notificaciones -->
    <div class="notif-list-card fade-in">
        <div class="notif-list-header">
            <i class="bi bi-list-ul"></i>
            <h2 class="notif-list-title">
                Notificaciones {{ $filter != 'all' ? '(' . ucfirst(str_replace('_', ' ', $filter)) . ')' : '' }}
            </h2>
        </div>

        @if($notificaciones->count() > 0)
            @foreach($notificaciones as $notificacion)
            <div class="notif-item {{ !$notificacion->leida ? 'unread' : '' }}" 
                 data-id="{{ $notificacion->id }}"
                 data-notif-id="{{ $notificacion->_id ?? $notificacion->id }}">
                <div class="notif-item-content">
                    <div class="notif-icon-wrapper">
                        @switch($notificacion->tipo)
                            @case('pedido')
                                <i class="bi bi-cart notif-icon" style="color: var(--info);"></i>
                                @break
                            @case('venta')
                                <i class="bi bi-currency-dollar notif-icon" style="color: var(--success);"></i>
                                @break
                            @case('usuario')
                                <i class="bi bi-person notif-icon" style="color: #8b5cf6;"></i>
                                @break
                            @case('comision')
                                <i class="bi bi-wallet notif-icon" style="color: var(--warning);"></i>
                                @break
                            @case('sistema')
                                <i class="bi bi-gear notif-icon" style="color: var(--gray-500);"></i>
                                @break
                            @default
                                <i class="bi bi-bell notif-icon" style="color: var(--gray-500);"></i>
                        @endswitch
                    </div>

                    <div class="notif-body">
                        <h3 class="notif-item-title">{{ $notificacion->titulo }}</h3>
                        <p class="notif-item-message">{{ $notificacion->mensaje }}</p>
                        
                        @if($notificacion->tipo === 'solicitud_retiro' && isset($notificacion->datos_adicionales['puede_cambiar_estado']) && $notificacion->datos_adicionales['puede_cambiar_estado'])
                            <div class="notif-solicitud-acciones mt-3"
                                 data-monto="{{ number_format($notificacion->datos_adicionales['monto'] ?? 0, 0, ',', '.') }}"
                                 data-metodo="{{ ucfirst($notificacion->datos_adicionales['metodo_pago'] ?? 'N/A') }}"
                                 data-vendedor="{{ $notificacion->datos_adicionales['vendedor_nombre'] ?? 'N/A' }}"
                                 data-datos-pago="{{ $notificacion->datos_adicionales['datos_pago'] ?? 'N/A' }}">
                                <div class="alert alert-info mb-3">
                                    <strong>Detalles de la solicitud:</strong><br>
                                    <small>
                                        Vendedor: {{ $notificacion->datos_adicionales['vendedor_nombre'] ?? 'N/A' }}<br>
                                        Método: {{ ucfirst($notificacion->datos_adicionales['metodo_pago'] ?? 'N/A') }}<br>
                                        Datos de pago: {{ $notificacion->datos_adicionales['datos_pago'] ?? 'N/A' }}<br>
                                        Estado actual: <span class="badge bg-warning">{{ ucfirst($notificacion->datos_adicionales['estado'] ?? 'pendiente') }}</span>
                                    </small>
                                </div>
                                
                                @if($notificacion->datos_adicionales['estado'] === 'pendiente')
                                    <div class="btn-group gap-2">
                                        <button class="btn btn-sm btn-success" 
                                                onclick="aprobarSolicitud('{{ $notificacion->datos_adicionales['solicitud_id'] }}', '{{ $notificacion->id }}')">
                                            <i class="bi bi-check-circle"></i> Aprobar
                                        </button>
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="marcarPagadoModal('{{ $notificacion->datos_adicionales['solicitud_id'] }}', '{{ $notificacion->id }}')">
                                            <i class="bi bi-cash-stack"></i> Marcar como Pagado
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="rechazarSolicitudModal('{{ $notificacion->datos_adicionales['solicitud_id'] }}', '{{ $notificacion->id }}')">
                                            <i class="bi bi-x-circle"></i> Rechazar
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <div class="notif-item-meta">
                            <small style="color: var(--gray-500);">
                                <i class="bi bi-clock me-1"></i>
                                {{ $notificacion->created_at->diffForHumans() }}
                            </small>
                            <span class="notif-badge notif-badge-{{ $notificacion->tipo }}">
                                {{ ucfirst($notificacion->tipo) }}
                            </span>
                            @if(!$notificacion->leida)
                                <span class="notif-badge notif-badge-nuevo">Nuevo</span>
                            @endif
                        </div>
                    </div>

                    <div class="notif-actions-wrapper">
                        @if(!$notificacion->leida)
                            <button class="notif-action-btn notif-action-btn-success"
                                    onclick="marcarLeida('{{ $notificacion->id }}')"
                                    title="Marcar como leída">
                                <i class="bi bi-check"></i>
                            </button>
                        @endif
                        <button class="notif-action-btn notif-action-btn-danger"
                                onclick="eliminarNotificacion('{{ $notificacion->id }}')"
                                title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Paginación -->
            <div class="p-3">
                {{ $notificaciones->withQueryString()->links() }}
            </div>
        @else
            <div class="notif-empty">
                <i class="bi bi-bell-slash notif-empty-icon"></i>
                <h3 class="notif-empty-title">No hay notificaciones</h3>
                <p class="notif-empty-text">
                    @if($filter == 'unread')
                        No tienes notificaciones sin leer
                    @elseif($filter == 'read')
                        No tienes notificaciones leídas
                    @else
                        No tienes notificaciones en este momento
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Modal Glassmorphism para Aprobar Solicitud -->
    <div id="aprobarModal" class="glass-modal-backdrop" onclick="closeAprobarModal(event)">
        <div class="glass-modal" onclick="event.stopPropagation()">
            <div class="glass-modal-header success">
                <div class="glass-modal-icon success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h3 class="glass-modal-title">Aprobar Solicitud</h3>
                <button type="button" class="glass-modal-close" onclick="closeAprobarModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="glass-modal-body">
                <div class="glass-modal-content">
                    <p class="glass-modal-text">
                        ¿Estás seguro de aprobar esta solicitud de retiro?
                    </p>
                    <div class="glass-info-card" id="aprobarInfoCard">
                        <!-- Información dinámica -->
                    </div>
                    <p class="glass-modal-note info">
                        <i class="bi bi-info-circle-fill"></i>
                        Al aprobar, la solicitud quedará lista para ser procesada y pagada.
                    </p>
                </div>
            </div>
            
            <div class="glass-modal-footer">
                <button type="button" class="glass-btn glass-btn-secondary" onclick="closeAprobarModal()">
                    <i class="bi bi-x-circle"></i>
                    Cancelar
                </button>
                <button type="button" class="glass-btn glass-btn-success" onclick="confirmarAprobar()">
                    <i class="bi bi-check-circle-fill"></i>
                    Confirmar Aprobación
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Glassmorphism para Rechazar Solicitud -->
    <div id="rechazarModal" class="glass-modal-backdrop" onclick="closeRechazarModal(event)">
        <div class="glass-modal" onclick="event.stopPropagation()">
            <div class="glass-modal-header danger">
                <div class="glass-modal-icon danger">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <h3 class="glass-modal-title">Rechazar Solicitud</h3>
                <button type="button" class="glass-modal-close" onclick="closeRechazarModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="glass-modal-body">
                <div class="glass-modal-content">
                    <p class="glass-modal-text">
                        ¿Estás seguro de rechazar esta solicitud de retiro?
                    </p>
                    <div class="glass-info-card" id="rechazarInfoCard">
                        <!-- Información dinámica -->
                    </div>
                    <form id="rechazarForm">
                        @csrf
                        <div class="glass-form-group">
                            <label class="glass-form-label">Motivo del Rechazo *</label>
                            <textarea class="glass-form-textarea" name="motivo" required 
                                      placeholder="Explica el motivo del rechazo para que el vendedor pueda corregir..."></textarea>
                        </div>
                    </form>
                    <p class="glass-modal-note warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Las comisiones serán devueltas al vendedor como disponibles.
                    </p>
                </div>
            </div>
            
            <div class="glass-modal-footer">
                <button type="button" class="glass-btn glass-btn-secondary" onclick="closeRechazarModal()">
                    <i class="bi bi-x-circle"></i>
                    Cancelar
                </button>
                <button type="submit" form="rechazarForm" class="glass-btn glass-btn-danger">
                    <i class="bi bi-x-circle-fill"></i>
                    Confirmar Rechazo
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Glassmorphism para Marcar como Pagado -->
    <div id="pagarModal" class="glass-modal-backdrop" onclick="closePagarModal(event)">
        <div class="glass-modal" onclick="event.stopPropagation()">
            <div class="glass-modal-header info">
                <div class="glass-modal-icon info">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h3 class="glass-modal-title">Marcar como Pagado</h3>
                <button type="button" class="glass-modal-close" onclick="closePagarModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="glass-modal-body">
                <div class="glass-modal-content">
                    <p class="glass-modal-text">
                        Confirma que el pago ha sido realizado correctamente
                    </p>
                    <div class="glass-info-card" id="pagarInfoCard">
                        <!-- Información dinámica -->
                    </div>
                    <form id="pagarForm">
                        @csrf
                        <div class="glass-form-group">
                            <label class="glass-form-label">Referencia de Pago</label>
                            <input type="text" class="glass-form-input" name="referencia_pago" 
                                   placeholder="Ej: TRX-123456789">
                        </div>
                        <div class="glass-form-group">
                            <label class="glass-form-label">Notas Adicionales</label>
                            <textarea class="glass-form-textarea" name="notas_pago" rows="3"
                                      placeholder="Cualquier observación sobre el pago realizado..."></textarea>
                        </div>
                    </form>
                    <p class="glass-modal-note info">
                        <i class="bi bi-shield-check"></i>
                        El vendedor recibirá una notificación confirmando el pago.
                    </p>
                </div>
            </div>
            
            <div class="glass-modal-footer">
                <button type="button" class="glass-btn glass-btn-secondary" onclick="closePagarModal()">
                    <i class="bi bi-x-circle"></i>
                    Cancelar
                </button>
                <button type="submit" form="pagarForm" class="glass-btn glass-btn-info">
                    <i class="bi bi-check2-circle"></i>
                    Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.notificacionesRoutes = {
    marcarLeida: '{{ route("admin.notificaciones.marcar-leida", ":id") }}',
    marcarTodasLeidas: '{{ route("admin.notificaciones.marcar-todas-leidas") }}',
    eliminar: '{{ route("admin.notificaciones.eliminar", ":id") }}',
    limpiarLeidas: '{{ route("admin.notificaciones.limpiar-leidas") }}',
    crearPruebas: '{{ route("admin.notificaciones.crear-pruebas") }}',
    aprobarSolicitud: '{{ route("admin.solicitudes-retiro.aprobar", ":id") }}',
    rechazarSolicitud: '{{ route("admin.solicitudes-retiro.rechazar", ":id") }}',
    marcarPagado: '{{ route("admin.solicitudes-retiro.marcar-pagado", ":id") }}'
};
window.notificacionesCSRF = '{{ csrf_token() }}';

// Variables globales para modales
let solicitudIdActual = null;
let notificacionIdActual = null;
let datosActuales = null;

// ========== MODAL APROBAR ==========
function aprobarSolicitud(solicitudId, notifId) {
    solicitudIdActual = solicitudId;
    notificacionIdActual = notifId;
    
    // Obtener datos de la notificación
    const notifElement = event.target.closest('.notif-item');
    if (notifElement) {
        const monto = notifElement.querySelector('[data-monto]')?.getAttribute('data-monto') || 'N/A';
        const metodo = notifElement.querySelector('[data-metodo]')?.getAttribute('data-metodo') || 'N/A';
        const vendedor = notifElement.querySelector('[data-vendedor]')?.getAttribute('data-vendedor') || 'N/A';
        
        document.getElementById('aprobarInfoCard').innerHTML = `
            <div class="glass-info-item">
                <span class="glass-info-label">Vendedor:</span>
                <span class="glass-info-value">${vendedor}</span>
            </div>
            <div class="glass-info-item">
                <span class="glass-info-label">Monto:</span>
                <span class="glass-info-value">$${monto}</span>
            </div>
            <div class="glass-info-item">
                <span class="glass-info-label">Método:</span>
                <span class="glass-info-value">${metodo}</span>
            </div>
        `;
    }
    
    openModal('aprobarModal');
}

function closeAprobarModal(event) {
    if (event && event.target !== event.currentTarget) return;
    closeModal('aprobarModal');
}

function confirmarAprobar() {
    if (!solicitudIdActual) return;
    
    fetch(window.notificacionesRoutes.aprobarSolicitud.replace(':id', solicitudIdActual), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.notificacionesCSRF
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error al procesar la solicitud');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success !== false) {
            showToast('Solicitud aprobada exitosamente', 'success');
            actualizarEstadoNotificacion(notificacionIdActual, 'aprobado');
            closeAprobarModal();
        } else {
            showToast(data.message || 'Error al aprobar la solicitud', 'error');
            closeAprobarModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message || 'Error al procesar la solicitud', 'error');
        closeAprobarModal();
    });
}

// ========== MODAL RECHAZAR ==========
function rechazarSolicitudModal(solicitudId, notifId) {
    solicitudIdActual = solicitudId;
    notificacionIdActual = notifId;
    
    // Obtener datos de la notificación
    const notifElement = event.target.closest('.notif-item');
    if (notifElement) {
        const monto = notifElement.querySelector('[data-monto]')?.getAttribute('data-monto') || 'N/A';
        const metodo = notifElement.querySelector('[data-metodo]')?.getAttribute('data-metodo') || 'N/A';
        const vendedor = notifElement.querySelector('[data-vendedor]')?.getAttribute('data-vendedor') || 'N/A';
        
        document.getElementById('rechazarInfoCard').innerHTML = `
            <div class="glass-info-item">
                <span class="glass-info-label">Vendedor:</span>
                <span class="glass-info-value">${vendedor}</span>
            </div>
            <div class="glass-info-item">
                <span class="glass-info-label">Monto:</span>
                <span class="glass-info-value">$${monto}</span>
            </div>
            <div class="glass-info-item">
                <span class="glass-info-label">Método:</span>
                <span class="glass-info-value">${metodo}</span>
            </div>
        `;
    }
    
    openModal('rechazarModal');
}

function closeRechazarModal(event) {
    if (event && event.target !== event.currentTarget) return;
    closeModal('rechazarModal');
    document.getElementById('rechazarForm').reset();
}

// Submit rechazar
document.getElementById('rechazarForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(window.notificacionesRoutes.rechazarSolicitud.replace(':id', solicitudIdActual), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.notificacionesCSRF
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error al procesar la solicitud');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success !== false) {
            showToast('Solicitud rechazada exitosamente', 'success');
            actualizarEstadoNotificacion(notificacionIdActual, 'rechazado', data.motivo);
            closeRechazarModal();
        } else {
            showToast(data.message || 'Error al rechazar la solicitud', 'error');
            closeRechazarModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message || 'Error al procesar la solicitud', 'error');
        closeRechazarModal();
    });
});

// ========== MODAL PAGAR ==========
function marcarPagadoModal(solicitudId, notifId) {
    solicitudIdActual = solicitudId;
    notificacionIdActual = notifId;
    
    // Obtener datos de la notificación
    const notifElement = event.target.closest('.notif-item');
    if (notifElement) {
        const monto = notifElement.querySelector('[data-monto]')?.getAttribute('data-monto') || 'N/A';
        const metodo = notifElement.querySelector('[data-metodo]')?.getAttribute('data-metodo') || 'N/A';
        const vendedor = notifElement.querySelector('[data-vendedor]')?.getAttribute('data-vendedor') || 'N/A';
        const datosPago = notifElement.querySelector('[data-datos-pago]')?.getAttribute('data-datos-pago') || 'N/A';
        
        document.getElementById('pagarInfoCard').innerHTML = `
            <div class="glass-info-item">
                <span class="glass-info-label">Vendedor:</span>
                <span class="glass-info-value">${vendedor}</span>
            </div>
            <div class="glass-info-item">
                <span class="glass-info-label">Monto:</span>
                <span class="glass-info-value">$${monto}</span>
            </div>
            <div class="glass-info-item">
                <span class="glass-info-label">Método:</span>
                <span class="glass-info-value">${metodo}</span>
            </div>
            <div class="glass-info-item">
                <span class="glass-info-label">Datos de Pago:</span>
                <span class="glass-info-value" style="font-size: 0.9rem; word-break: break-word;">${datosPago}</span>
            </div>
        `;
    }
    
    openModal('pagarModal');
}

function closePagarModal(event) {
    if (event && event.target !== event.currentTarget) return;
    closeModal('pagarModal');
    document.getElementById('pagarForm').reset();
}

// Submit pagar
document.getElementById('pagarForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(window.notificacionesRoutes.marcarPagado.replace(':id', solicitudIdActual), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.notificacionesCSRF
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error al procesar la solicitud');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success !== false) {
            showToast('Solicitud marcada como pagada exitosamente', 'success');
            actualizarEstadoNotificacion(notificacionIdActual, 'pagado', null, data.referencia);
            closePagarModal();
        } else {
            showToast(data.message || 'Error al marcar como pagado', 'error');
            closePagarModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message || 'Error al procesar la solicitud', 'error');
        closePagarModal();
    });
});

// ========== ACTUALIZAR ESTADO VISUAL DE NOTIFICACIÓN ==========
function actualizarEstadoNotificacion(notifId, nuevoEstado, motivo = null, referencia = null) {
    console.log('Actualizando estado de notificación', {notifId, nuevoEstado, motivo, referencia});
    
    // Convertir a string para comparación consistente
    const notifIdStr = String(notifId);
    
    // Buscar el contenedor de la notificación
    const notifItems = document.querySelectorAll('.notif-item');
    let notifElement = null;
    
    // Buscar por diferentes métodos
    notifItems.forEach(item => {
        // Método 1: Por atributo data-notif-id
        const itemNotifId = item.getAttribute('data-notif-id');
        if (itemNotifId && String(itemNotifId) === notifIdStr) {
            notifElement = item;
            console.log('Notificación encontrada por data-notif-id');
            return;
        }
        
        // Método 2: Por atributo data-id
        const itemDataId = item.getAttribute('data-id');
        if (itemDataId && String(itemDataId) === notifIdStr) {
            notifElement = item;
            console.log('Notificación encontrada por data-id');
            return;
        }
        
        // Método 3: Por botones que contienen el notifId
        const actions = item.querySelector('.notif-solicitud-acciones');
        if (actions) {
            const buttons = actions.querySelectorAll('button');
            buttons.forEach(btn => {
                const onclick = btn.getAttribute('onclick');
                if (onclick && onclick.includes(notifIdStr)) {
                    notifElement = item;
                    console.log('Notificación encontrada por onclick');
                }
            });
        }
    });
    
    if (!notifElement) {
        console.error('No se encontró el elemento de notificación con ID:', notifId);
        console.log('IDs de notificaciones disponibles:', 
            Array.from(notifItems).map(item => ({
                dataNotifId: item.getAttribute('data-notif-id'),
                dataId: item.getAttribute('data-id')
            }))
        );
        // Intentar recargar la página como fallback
        setTimeout(() => {
            console.log('Recargando página como fallback...');
            location.reload();
        }, 2000);
        return;
    }
    
    const accionesDiv = notifElement.querySelector('.notif-solicitud-acciones');
    if (!accionesDiv) {
        console.error('No se encontró el div de acciones en la notificación');
        setTimeout(() => location.reload(), 2000);
        return;
    }
    
    console.log('Actualizando UI de la notificación...');
    
    // Eliminar botones de acción con animación
    const botonesDiv = accionesDiv.querySelector('.btn-group');
    if (botonesDiv) {
        botonesDiv.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        botonesDiv.style.opacity = '0';
        botonesDiv.style.transform = 'scale(0.95)';
        setTimeout(() => {
            botonesDiv.remove();
            console.log('Botones eliminados');
        }, 300);
    }
    
    // Actualizar el badge de estado
    const estadoBadge = accionesDiv.querySelector('.badge');
    if (estadoBadge) {
        estadoBadge.style.transition = 'all 0.3s ease';
        estadoBadge.className = 'badge';
        let textoEstado = '';
        let claseEstado = '';
        
        switch(nuevoEstado) {
            case 'aprobado':
                claseEstado = 'bg-info';
                textoEstado = 'Aprobado';
                break;
            case 'rechazado':
                claseEstado = 'bg-danger';
                textoEstado = 'Rechazado';
                break;
            case 'pagado':
                claseEstado = 'bg-success';
                textoEstado = 'Pagado';
                break;
        }
        
        estadoBadge.classList.add(claseEstado);
        estadoBadge.textContent = textoEstado;
        estadoBadge.style.transform = 'scale(1.1)';
        setTimeout(() => {
            estadoBadge.style.transform = 'scale(1)';
        }, 200);
        console.log('Badge actualizado a:', textoEstado);
    }
    
    // Crear badge de estado procesado
    setTimeout(() => {
        const estadoHTML = crearBadgeEstado(nuevoEstado, motivo, referencia);
        accionesDiv.insertAdjacentHTML('beforeend', estadoHTML);
        console.log('Badge de estado insertado');
        
        // Agregar animación de actualización
        accionesDiv.style.animation = 'pulse 0.6s ease-in-out';
        setTimeout(() => {
            accionesDiv.style.animation = '';
        }, 600);
        
        // Scroll suave hacia la notificación actualizada
        notifElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 350);
}

function crearBadgeEstado(estado, motivo = null, referencia = null) {
    let html = '<div class="alert mt-3 fade-in-animation" style="animation: slideInUp 0.5s ease-out;">';
    
    switch(estado) {
        case 'aprobado':
            html += `
                <div class="alert alert-success" role="alert">
                    <h6 class="alert-heading mb-2">
                        <i class="bi bi-check-circle-fill"></i> ✅ Solicitud Aprobada
                    </h6>
                    <p class="mb-0 small">
                        Esta solicitud ha sido aprobada exitosamente y está lista para ser procesada.
                    </p>
                    <hr class="my-2">
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> Procesado hace instantes
                    </small>
                </div>
            `;
            break;
            
        case 'rechazado':
            html += `
                <div class="alert alert-danger" role="alert">
                    <h6 class="alert-heading mb-2">
                        <i class="bi bi-x-circle-fill"></i> ❌ Solicitud Rechazada
                    </h6>
                    <p class="mb-2 small">
                        Esta solicitud ha sido rechazada. Las comisiones han sido devueltas al vendedor.
                    </p>
                    ${motivo ? `
                        <div class="bg-white p-2 rounded border border-danger mb-2">
                            <strong class="small">Motivo del rechazo:</strong><br>
                            <small class="text-dark">${motivo}</small>
                        </div>
                    ` : ''}
                    <hr class="my-2">
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> Procesado hace instantes
                    </small>
                </div>
            `;
            break;
            
        case 'pagado':
            html += `
                <div class="alert alert-success" role="alert">
                    <h6 class="alert-heading mb-2">
                        <i class="bi bi-cash-stack"></i> 💰 Solicitud Pagada
                    </h6>
                    <p class="mb-2 small">
                        Esta solicitud ha sido pagada exitosamente. El vendedor ha recibido su pago.
                    </p>
                    ${referencia ? `
                        <div class="bg-white p-2 rounded border border-success mb-2">
                            <strong class="small">Referencia de pago:</strong><br>
                            <code class="small">${referencia}</code>
                        </div>
                    ` : ''}
                    <hr class="my-2">
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> Procesado hace instantes
                    </small>
                </div>
            `;
            break;
    }
    
    html += '</div>';
    return html;
}

// ========== FUNCIONES GENERALES DE MODAL ==========
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Cerrar modales con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAprobarModal();
        closeRechazarModal();
        closePagarModal();
    }
});

// ========== TOAST NOTIFICATIONS ==========
function showToast(message, type = 'info') {
    // Crear toast container si no existe
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10001;';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show`;
    toast.style.cssText = 'min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 150);
    }, 3000);
}
</script>
<script src="{{ asset('js/admin/notificaciones-modern.js') }}?v={{ time() }}"></script>
@endpush
