@extends('layouts.vendedor')

@section('title', 'Mi Perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/perfil-modern.css') }}?v={{ filemtime(public_path('css/vendedor/perfil-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid px-4">
    
    {{-- Header Hero del Perfil --}}
    <div class="perfil-header fade-in-up">
        <div class="perfil-header-content">
            <div class="perfil-avatar-container">
                @if($vendedor->avatar)
                    <img src="{{ Storage::url($vendedor->avatar) }}" alt="{{ $vendedor->name }}" class="perfil-avatar-large">
                @else
                    <div class="perfil-avatar-large" style="background: linear-gradient(135deg, var(--wine), var(--wine-dark)); display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 700;">
                        {{ strtoupper(substr($vendedor->name, 0, 1)) }}
                    </div>
                @endif
                <div class="perfil-avatar-badge">
                    <i class="bi bi-check"></i>
                </div>
            </div>
            
            <div class="perfil-header-info">
                <h1>{{ $vendedor->name }} {{ $vendedor->apellidos }}</h1>
                
                <div class="perfil-header-meta">
                    <div class="perfil-meta-item">
                        <i class="bi bi-envelope"></i>
                        <span>{{ $vendedor->email }}</span>
                    </div>
                    @if($vendedor->telefono)
                    <div class="perfil-meta-item">
                        <i class="bi bi-phone"></i>
                        <span>{{ $vendedor->telefono }}</span>
                    </div>
                    @endif
                    @if($vendedor->ciudad)
                    <div class="perfil-meta-item">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ $vendedor->ciudad }}</span>
                    </div>
                    @endif
                    <div class="perfil-meta-item">
                        <i class="bi bi-calendar3"></i>
                        <span>Miembro desde {{ $vendedor->created_at->format('M Y') }}</span>
                    </div>
                </div>
                
                <div class="perfil-header-actions">
                    <a href="{{ route('vendedor.perfil.edit') }}" class="perfil-action-btn perfil-action-btn-primary">
                        <i class="bi bi-pencil"></i> Editar Perfil
                    </a>
                    <button onclick="perfilManager.showModal('change-password-modal')" class="perfil-action-btn perfil-action-btn-secondary">
                        <i class="bi bi-shield-lock"></i> Cambiar Contraseña
                    </button>
                    <button onclick="perfilManager.exportData()" class="perfil-action-btn perfil-action-btn-secondary" id="exportDataBtn">
                        <i class="bi bi-download"></i> Exportar Datos
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Stats Cards --}}
    <div class="perfil-stats-container">
        <div class="perfil-stat-card stat-success fade-in-up animate-delay-1">
            <div class="perfil-stat-header">
                <div class="perfil-stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
            <div class="perfil-stat-value">${{ format_currency($stats['total_ventas'] ?? 0) }}</div>
            <div class="perfil-stat-label">Ventas Totales</div>
            <div class="perfil-stat-trend trend-up">
                <i class="bi bi-arrow-up-circle-fill"></i>
                <span>+${{ format_currency($stats['ventas_mes'] ?? 0) }} este mes</span>
            </div>
        </div>
        
        <div class="perfil-stat-card stat-info fade-in-up animate-delay-2">
            <div class="perfil-stat-header">
                <div class="perfil-stat-icon">
                    <i class="bi bi-cart-check"></i>
                </div>
            </div>
            <div class="perfil-stat-value">{{ $stats['total_pedidos'] ?? 0 }}</div>
            <div class="perfil-stat-label">Pedidos Completados</div>
            <div class="perfil-stat-trend trend-up">
                <i class="bi bi-arrow-up-circle-fill"></i>
                <span>{{ $stats['pedidos_mes'] ?? 0 }} este mes</span>
            </div>
        </div>
        
        <div class="perfil-stat-card stat-warning fade-in-up animate-delay-3">
            <div class="perfil-stat-header">
                <div class="perfil-stat-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
            <div class="perfil-stat-value">${{ format_currency($stats['total_comisiones'] ?? 0) }}</div>
            <div class="perfil-stat-label">Comisiones Ganadas</div>
            <div class="perfil-stat-trend trend-up">
                <i class="bi bi-graph-up"></i>
                <span>Total acumulado</span>
            </div>
        </div>
        
        <div class="perfil-stat-card stat-danger fade-in-up animate-delay-4">
            <div class="perfil-stat-header">
                <div class="perfil-stat-icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="perfil-stat-value">{{ $stats['total_referidos'] ?? 0 }}</div>
            <div class="perfil-stat-label">Referidos Activos</div>
            <div class="perfil-stat-trend">
                <i class="bi bi-person-plus"></i>
                <span>Red de ventas</span>
            </div>
        </div>
    </div>
    
    {{-- Tabs de Contenido --}}
    <div class="perfil-tabs-container scale-in">
        <div class="perfil-tabs">
            <button class="perfil-tab active" data-tab="informacion">
                <i class="bi bi-person-circle"></i> Información Personal
            </button>
            <button class="perfil-tab" data-tab="actividad">
                <i class="bi bi-clock-history"></i> Actividad Reciente
            </button>
            <button class="perfil-tab" data-tab="estadisticas">
                <i class="bi bi-graph-up-arrow"></i> Estadísticas
            </button>
            <button class="perfil-tab" data-tab="configuracion">
                <i class="bi bi-gear"></i> Configuración
            </button>
        </div>
        
        {{-- Tab: Información Personal --}}
        <div class="perfil-tab-content active" id="informacion">
            <div class="perfil-info-grid">
                <div class="perfil-info-section">
                    <h3><i class="bi bi-person-badge"></i> Datos Personales</h3>
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Nombre Completo</span>
                        <span class="perfil-info-value">{{ $vendedor->name }} {{ $vendedor->apellidos ?? '' }}</span>
                    </div>
                    @if($vendedor->cedula)
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Cédula</span>
                        <span class="perfil-info-value">{{ $vendedor->cedula }}</span>
                    </div>
                    @endif
                    @if($vendedor->fecha_nacimiento)
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Fecha de Nacimiento</span>
                        <span class="perfil-info-value">{{ \Carbon\Carbon::parse($vendedor->fecha_nacimiento)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($vendedor->biografia || $vendedor->bio)
                    <div class="perfil-info-item" style="flex-direction: column; align-items: flex-start;">
                        <span class="perfil-info-label">Biografía</span>
                        <span class="perfil-info-value" style="text-align: left; margin-top: 0.5rem;">{{ $vendedor->biografia ?? $vendedor->bio }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="perfil-info-section">
                    <h3><i class="bi bi-telephone"></i> Contacto</h3>
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Email</span>
                        <span class="perfil-info-value">{{ $vendedor->email }}</span>
                    </div>
                    @if($vendedor->telefono)
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Teléfono</span>
                        <span class="perfil-info-value">{{ $vendedor->telefono }}</span>
                    </div>
                    @endif
                    @if($vendedor->direccion)
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Dirección</span>
                        <span class="perfil-info-value">{{ $vendedor->direccion }}</span>
                    </div>
                    @endif
                    @if($vendedor->ciudad)
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Ciudad</span>
                        <span class="perfil-info-value">{{ $vendedor->ciudad }}</span>
                    </div>
                    @endif
                    @if($vendedor->departamento)
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Departamento</span>
                        <span class="perfil-info-value">{{ $vendedor->departamento }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="perfil-info-section">
                    <h3><i class="bi bi-briefcase"></i> Información Profesional</h3>
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Rol</span>
                        <span class="perfil-info-value">
                            <span class="perfil-badge perfil-badge-primary">{{ ucfirst($vendedor->rol) }}</span>
                        </span>
                    </div>
                    @if($vendedor->codigo_referido)
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Código de Referido</span>
                        <span class="perfil-info-value">
                            <button onclick="perfilManager.copyToClipboard('{{ $vendedor->codigo_referido }}')" 
                                    class="perfil-badge perfil-badge-info" 
                                    style="cursor: pointer; border: none; background: rgba(59,130,246,0.1);"
                                    title="Clic para copiar">
                                {{ $vendedor->codigo_referido }} <i class="bi bi-clipboard"></i>
                            </button>
                        </span>
                    </div>
                    @endif
                    @if($vendedor->nivel_vendedor)
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Nivel</span>
                        <span class="perfil-info-value">
                            <span class="perfil-badge perfil-badge-success">{{ $vendedor->nivel_vendedor }}</span>
                        </span>
                    </div>
                    @endif
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Días Activo</span>
                        <span class="perfil-info-value">{{ $stats['dias_activo'] ?? 0 }} días</span>
                    </div>
                </div>
                
                @if($vendedor->meta_mensual && to_float($vendedor->meta_mensual) > 0)
                <div class="perfil-info-section" style="grid-column: 1 / -1;">
                    <h3><i class="bi bi-bullseye"></i> Meta Mensual</h3>
                    <div class="perfil-progress-container">
                        <div class="perfil-progress-bar">
                            <div class="perfil-progress-fill" data-progress="{{ min($stats['porcentaje_meta'] ?? 0, 100) }}"></div>
                        </div>
                        <div class="perfil-progress-label">
                            <span class="perfil-progress-label-start">${{ format_currency($stats['ventas_mes'] ?? 0) }}</span>
                            <span class="perfil-progress-label-end">${{ format_currency($vendedor->meta_mensual ?? 0) }} ({{ format_number($stats['porcentaje_meta'] ?? 0, 1) }}%)</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        {{-- Tab: Actividad Reciente --}}
        <div class="perfil-tab-content" id="actividad">
            <div class="perfil-activity-container">
                <div class="perfil-activity-header">
                    <h3><i class="bi bi-clock-history"></i> Últimas Actividades</h3>
                    <button onclick="perfilManager.refreshStats()" class="perfil-btn perfil-btn-secondary" id="refreshStatsBtn" style="padding: 0.5rem 1rem;">
                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                    </button>
                </div>
                
                @if(!empty($actividadReciente) && count($actividadReciente) > 0)
                <div class="perfil-activity-timeline">
                    @foreach($actividadReciente as $actividad)
                    <div class="perfil-activity-item">
                        <div class="perfil-activity-icon icon-{{ $actividad['color'] }}">
                            <i class="bi {{ $actividad['icono'] }}"></i>
                        </div>
                        <div class="perfil-activity-content">
                            <div class="perfil-activity-title">{{ $actividad['descripcion'] }}</div>
                            @if($actividad['tipo'] != 'referido')
                            <div class="perfil-activity-desc">Tipo: {{ ucfirst($actividad['tipo']) }}</div>
                            @endif
                            <div class="perfil-activity-meta">
                                <span>{{ $actividad['fecha']->diffForHumans() }}</span>
                                @if($actividad['monto'])
                                <span class="perfil-activity-amount">${{ format_currency($actividad['monto']) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center" style="padding: 3rem 1rem; color: var(--gray-500);">
                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                    <p style="margin-top: 1rem; font-weight: 600;">No hay actividad reciente</p>
                    <p style="font-size: 0.9rem;">Tus actividades aparecerán aquí</p>
                </div>
                @endif
            </div>
        </div>
        
        {{-- Tab: Estadísticas --}}
        <div class="perfil-tab-content" id="estadisticas">
            <div class="perfil-info-grid">
                <div class="perfil-info-section">
                    <h3><i class="bi bi-graph-up"></i> Rendimiento de Ventas</h3>
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Promedio por Venta</span>
                        <span class="perfil-info-value">${{ format_currency($stats['promedio_venta'] ?? 0) }}</span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Ventas Este Mes</span>
                        <span class="perfil-info-value">${{ format_currency($stats['ventas_mes'] ?? 0) }}</span>
                    </div>
                    @if(isset($stats['mejor_mes']) && $stats['mejor_mes'])
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Mejor Mes</span>
                        <span class="perfil-info-value">
                            {{ $stats['mejor_mes']['fecha'] }}<br>
                            <small class="text-success">${{ format_currency($stats['mejor_mes']['total']) }}</small>
                        </span>
                    </div>
                    @endif
                </div>
                
                <div class="perfil-info-section">
                    <h3><i class="bi bi-cash-stack"></i> Comisiones</h3>
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Total Ganadas</span>
                        <span class="perfil-info-value">${{ format_currency($stats['total_comisiones'] ?? 0) }}</span>
                    </div>
                    @if(isset($vendedor->comisiones_disponibles))
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Disponibles</span>
                        <span class="perfil-info-value text-success">${{ format_currency($vendedor->comisiones_disponibles ?? 0) }}</span>
                    </div>
                    @endif
                    @if(isset($vendedor->comisiones_ganadas))
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Pagadas</span>
                        <span class="perfil-info-value">${{ format_currency($vendedor->comisiones_ganadas ?? 0) }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="perfil-info-section">
                    <h3><i class="bi bi-people"></i> Red de Referidos</h3>
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Total Referidos</span>
                        <span class="perfil-info-value">{{ $stats['total_referidos'] ?? 0 }}</span>
                    </div>
                    @if(isset($vendedor->total_referidos))
                    <div class="perfil-info-item">
                        <span class="perfil-info-label">Activos</span>
                        <span class="perfil-info-value text-success">{{ $vendedor->total_referidos ?? 0 }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Tab: Configuración --}}
        <div class="perfil-tab-content" id="configuracion">
            <div class="perfil-config-container">

                {{-- Privacidad y Visibilidad --}}
                <div class="perfil-config-section">
                    <div class="perfil-config-section-header">
                        <h4><i class="bi bi-shield-check"></i> Privacidad y Visibilidad</h4>
                        <p style="color: var(--gray-600); font-size: 0.875rem; margin-top: 0.5rem;">Controla quién puede ver tu información personal</p>
                    </div>

                    <div class="perfil-config-item">
                        <div class="perfil-config-info">
                            <h5>Perfil Público</h5>
                            <p>Permitir que otros vendedores en la red vean tu perfil básico y estadísticas</p>
                        </div>
                        <label class="perfil-switch">
                            <input type="checkbox" id="perfil_publico" {{ ($vendedor->perfil_publico ?? false) ? 'checked' : '' }} onchange="guardarConfiguracion()">
                            <span class="perfil-switch-slider"></span>
                        </label>
                    </div>

                    <div class="perfil-config-item">
                        <div class="perfil-config-info">
                            <h5>Mostrar Teléfono Públicamente</h5>
                            <p>Hacer visible tu número de teléfono a otros usuarios y clientes potenciales</p>
                        </div>
                        <label class="perfil-switch">
                            <input type="checkbox" id="mostrar_telefono" {{ ($vendedor->mostrar_telefono ?? false) ? 'checked' : '' }} onchange="guardarConfiguracion()">
                            <span class="perfil-switch-slider"></span>
                        </label>
                    </div>

                    <div class="perfil-config-item">
                        <div class="perfil-config-info">
                            <h5>Compartir Estadísticas</h5>
                            <p>Permitir que tu líder y equipo vean tus estadísticas de desempeño y métricas de ventas</p>
                        </div>
                        <label class="perfil-switch">
                            <input type="checkbox" id="mostrar_stats" {{ ($vendedor->mostrar_stats ?? true) ? 'checked' : '' }} onchange="guardarConfiguracion()">
                            <span class="perfil-switch-slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Información de Cuenta --}}
                <div class="perfil-config-section">
                    <div class="perfil-config-section-header">
                        <h4><i class="bi bi-person-lock"></i> Seguridad de la Cuenta</h4>
                        <p style="color: var(--gray-600); font-size: 0.875rem; margin-top: 0.5rem;">Mantén tu cuenta protegida</p>
                    </div>

                    <div class="perfil-info-item" style="padding: 1rem; background: var(--gray-50); border-radius: 12px;">
                        <span class="perfil-info-label">Cambiar Contraseña</span>
                        <button onclick="perfilManager.showModal('change-password-modal')" class="perfil-btn perfil-btn-secondary" style="padding: 0.5rem 1rem;">
                            <i class="bi bi-shield-lock"></i> Cambiar
                        </button>
                    </div>

                    <div class="perfil-info-item" style="padding: 1rem; background: var(--gray-50); border-radius: 12px;">
                        <span class="perfil-info-label">Última Modificación</span>
                        <span class="perfil-info-value">{{ $vendedor->updated_at->diffForHumans() }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal: Cambiar Contraseña --}}
<div class="perfil-modal-backdrop" id="change-password-modal">
    <div class="perfil-modal">
        <div class="perfil-modal-header">
            <h3><i class="bi bi-shield-lock"></i> Cambiar Contraseña</h3>
            <button class="perfil-modal-close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form action="{{ route('vendedor.perfil.update-password') }}" method="POST" id="passwordForm" class="perfil-form">
            @csrf
            @method('PUT')
            <div class="perfil-modal-body">
                <div class="perfil-form-group">
                    <label class="perfil-form-label required">Contraseña Actual</label>
                    <input type="password" name="current_password" class="perfil-form-control" required>
                </div>
                
                <div class="perfil-form-group" style="margin-top: 1rem;">
                    <label class="perfil-form-label required">Nueva Contraseña</label>
                    <input type="password" name="password" id="password" class="perfil-form-control" required>
                    <span class="perfil-form-help">Mínimo 8 caracteres</span>
                </div>
                
                <div class="perfil-form-group" style="margin-top: 1rem;">
                    <label class="perfil-form-label required">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" class="perfil-form-control" required>
                </div>
            </div>
            <div class="perfil-modal-footer">
                <button type="button" class="perfil-btn perfil-btn-secondary" onclick="perfilManager.closeModal(document.getElementById('change-password-modal'))">
                    Cancelar
                </button>
                <button type="submit" class="perfil-btn perfil-btn-primary">
                    <i class="bi bi-check-circle"></i> Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Container --}}
<div class="perfil-toast-container"></div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        perfilManager.showToast('{{ session('success') }}', 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        perfilManager.showToast('{{ session('error') }}', 'danger');
    });
</script>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/perfil-modern.js') }}?v={{ filemtime(public_path('js/vendedor/perfil-modern.js')) }}"></script>
<script>
// Función para guardar configuración automáticamente
function guardarConfiguracion() {
    const configuracion = {
        perfil_publico: document.getElementById('perfil_publico').checked,
        mostrar_telefono: document.getElementById('mostrar_telefono').checked,
        mostrar_stats: document.getElementById('mostrar_stats').checked
    };

    // Mostrar indicador de guardado
    perfilManager.showToast('Guardando configuración...', 'info');

    fetch('{{ route('vendedor.perfil.update') }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(configuracion)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            perfilManager.showToast('Configuración guardada exitosamente', 'success');
        } else {
            perfilManager.showToast(data.message || 'Error al guardar configuración', 'danger');
        }
    })
    .catch(error => {
        perfilManager.showToast('Error de conexión al guardar', 'danger');
        console.error('Error:', error);
    });
}
</script>
@endpush
