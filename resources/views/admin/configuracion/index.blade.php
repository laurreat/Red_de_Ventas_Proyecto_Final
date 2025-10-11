@extends('layouts.admin')

@section('title', 'Configuración del Sistema')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/configuracion-modern.css') }}?v={{ filemtime(public_path('css/admin/configuracion-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="config-header animate-fadeInUp">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1><i class="bi bi-gear-fill me-2"></i>Configuración del Sistema</h1>
                <p>Administra todas las configuraciones de Arepa la Llanerita</p>
            </div>
            <div class="header-actions">
                <button class="config-action-btn-info" data-config-action="info">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Info Sistema</span>
                </button>
                <button class="config-action-btn-warning" data-config-action="cache">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Limpiar Cache</span>
                </button>
                <button class="config-action-btn-success" data-config-action="backup">
                    <i class="bi bi-cloud-download-fill"></i>
                    <span>Crear Backup</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="config-stat-card animate-delay-1">
                <i class="bi bi-people-fill text-primary"></i>
                <h4>{{ $estadisticas['usuarios_totales'] ?? 0 }}</h4>
                <small>Usuarios Totales</small>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="config-stat-card animate-delay-2">
                <i class="bi bi-cart-fill" style="color:var(--success)"></i>
                <h4>{{ $estadisticas['pedidos_totales'] ?? 0 }}</h4>
                <small>Pedidos Totales</small>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="config-stat-card animate-delay-3">
                <i class="bi bi-box-seam-fill" style="color:var(--warning)"></i>
                <h4>{{ $estadisticas['productos_totales'] ?? 0 }}</h4>
                <small>Productos</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="config-stat-card animate-delay-4">
                <i class="bi bi-currency-dollar" style="color:var(--info)"></i>
                <h4>${{ number_format($estadisticas['ventas_mes_actual'] ?? 0, 0, ',', '.') }}</h4>
                <small>Ventas Este Mes</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="config-stat-card animate-delay-4">
                <i class="bi bi-hdd-fill text-secondary"></i>
                <h4>{{ is_array($estadisticas['espacio_storage']) ? $estadisticas['espacio_storage']['usado'] : 'N/A' }}</h4>
                <small>Espacio Usado</small>
            </div>
        </div>
    </div>

    {{-- Configuración General --}}
    <div class="config-section-card animate-fadeInUp animate-delay-1">
        <div class="config-card-header">
            <h5><i class="bi bi-building"></i>Configuración General</h5>
            <button class="config-action-btn-outline" data-bs-toggle="collapse" data-bs-target="#configGeneral">
                <i class="bi bi-pencil-fill"></i>
                <span>Editar</span>
            </button>
        </div>
        <div class="config-card-body">
            <form method="POST" action="{{ route('admin.configuracion.update-general') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="config-form-label">Nombre de la Empresa</label>
                        <input type="text" name="nombre_empresa" class="form-control config-form-control"
                               value="{{ $configuraciones['general']['nombre_empresa'] ?? 'Arepa la Llanerita' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="config-form-label">Email de Contacto</label>
                        <input type="email" name="email_empresa" class="form-control config-form-control"
                               value="{{ $configuraciones['general']['email_empresa'] ?? 'info@arepallanerita.com' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="config-form-label">Teléfono</label>
                        <input type="text" name="telefono_empresa" class="form-control config-form-control"
                               value="{{ $configuraciones['general']['telefono_empresa'] ?? '+57 300 123 4567' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="config-form-label">Dirección</label>
                        <textarea name="direccion_empresa" class="form-control config-form-control" rows="3" readonly>{{ $configuraciones['general']['direccion_empresa'] ?? 'Calle 123 #45-67, Bogotá, Colombia' }}</textarea>
                    </div>
                </div>
                <div class="collapse" id="configGeneral">
                    <div class="border-top pt-3 d-flex gap-2">
                        <button type="submit" class="config-action-btn-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Guardar Cambios</span>
                        </button>
                        <button type="button" class="config-action-btn-secondary" data-bs-toggle="collapse" data-bs-target="#configGeneral">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>Cancelar</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Configuración MLM --}}
    <div class="config-section-card animate-fadeInUp animate-delay-2">
        <div class="config-card-header">
            <h5><i class="bi bi-diagram-3-fill"></i>Configuración MLM / Red de Ventas</h5>
            <button class="config-action-btn-outline" data-bs-toggle="collapse" data-bs-target="#configMlm">
                <i class="bi bi-pencil-fill"></i>
                <span>Editar</span>
            </button>
        </div>
        <div class="config-card-body">
            <form method="POST" action="{{ route('admin.configuracion.update-mlm') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Comisión Directa (%)</label>
                        <input type="number" name="comision_directa" class="form-control config-form-control"
                               step="0.1" min="0" max="100" value="{{ $configuraciones['mlm']['comision_directa'] ?? 10.0 }}" readonly>
                        <small class="text-muted">Comisión por venta directa</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Comisión por Referido (%)</label>
                        <input type="number" name="comision_referido" class="form-control config-form-control"
                               step="0.1" min="0" max="100" value="{{ $configuraciones['mlm']['comision_referido'] ?? 3.0 }}" readonly>
                        <small class="text-muted">Comisión por ventas de referidos</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Comisión de Líder (%)</label>
                        <input type="number" name="comision_lider" class="form-control config-form-control"
                               step="0.1" min="0" max="100" value="{{ $configuraciones['mlm']['comision_lider'] ?? 2.0 }}" readonly>
                        <small class="text-muted">Comisión adicional para líderes</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Bonificación Líder (%)</label>
                        <input type="number" name="bonificacion_lider" class="form-control config-form-control"
                               step="0.1" min="0" max="100" value="{{ $configuraciones['mlm']['bonificacion_lider'] ?? 5.0 }}" readonly>
                        <small class="text-muted">Bonificación por liderazgo</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Niveles Máximos</label>
                        <input type="number" name="niveles_maximos" class="form-control config-form-control"
                               min="1" max="10" value="{{ $configuraciones['mlm']['niveles_maximos'] ?? 5 }}" readonly>
                        <small class="text-muted">Profundidad máxima de la red</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Mínimo Ventas/Mes (COP)</label>
                        <input type="number" name="minimo_ventas_mes" class="form-control config-form-control"
                               min="0" value="{{ $configuraciones['mlm']['minimo_ventas_mes'] ?? 100000 }}" readonly>
                        <small class="text-muted">Ventas mínimas mensuales</small>
                    </div>
                </div>
                <div class="collapse" id="configMlm">
                    <div class="border-top pt-3 d-flex gap-2">
                        <button type="submit" class="config-action-btn-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Guardar Configuración MLM</span>
                        </button>
                        <button type="button" class="config-action-btn-secondary" data-bs-toggle="collapse" data-bs-target="#configMlm">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>Cancelar</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Configuración de Pedidos --}}
    <div class="config-section-card animate-fadeInUp animate-delay-3">
        <div class="config-card-header">
            <h5><i class="bi bi-cart-check-fill"></i>Configuración de Pedidos</h5>
            <button class="config-action-btn-outline" data-bs-toggle="collapse" data-bs-target="#configPedidos">
                <i class="bi bi-pencil-fill"></i>
                <span>Editar</span>
            </button>
        </div>
        <div class="config-card-body">
            <form method="POST" action="{{ route('admin.configuracion.update-pedidos') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Tiempo de Preparación (min)</label>
                        <input type="number" name="tiempo_preparacion" class="form-control config-form-control"
                               min="5" max="180" value="{{ $configuraciones['pedidos']['tiempo_preparacion'] ?? 30 }}" readonly>
                        <small class="text-muted">Tiempo estimado de preparación</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Costo de Envío (COP)</label>
                        <input type="number" name="costo_envio" class="form-control config-form-control"
                               min="0" value="{{ $configuraciones['pedidos']['costo_envio'] ?? 5000 }}" readonly>
                        <small class="text-muted">Costo base de envío</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="config-form-label">Envío Gratis Desde (COP)</label>
                        <input type="number" name="envio_gratis_desde" class="form-control config-form-control"
                               min="0" value="{{ $configuraciones['pedidos']['envio_gratis_desde'] ?? 50000 }}" readonly>
                        <small class="text-muted">Monto mínimo para envío gratis</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <h6 class="config-form-label">Estados de Pedidos Disponibles:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($configuraciones['pedidos']['estados_disponibles'] ?? [] as $key => $estado)
                                <span class="config-badge-primary">{{ $estado }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="collapse" id="configPedidos">
                    <div class="border-top pt-3 d-flex gap-2">
                        <button type="submit" class="config-action-btn-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Guardar Configuración de Pedidos</span>
                        </button>
                        <button type="button" class="config-action-btn-secondary" data-bs-toggle="collapse" data-bs-target="#configPedidos">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>Cancelar</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        {{-- Notificaciones --}}
        <div class="col-xl-6 mb-4">
            <div class="config-section-card animate-fadeInUp animate-delay-4">
                <div class="config-card-header">
                    <h5><i class="bi bi-bell-fill"></i>Configuración de Notificaciones</h5>
                    <button class="config-action-btn-outline" data-bs-toggle="collapse" data-bs-target="#configNotif">
                        <i class="bi bi-pencil-fill"></i>
                        <span>Editar</span>
                    </button>
                </div>
                <div class="config-card-body">
                    <form method="POST" action="{{ route('admin.configuracion.update-notificaciones') }}">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_pedidos" id="email_pedidos"
                                   {{ ($configuraciones['notificaciones']['email_pedidos'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label config-form-label" for="email_pedidos">
                                Notificaciones de nuevos pedidos por email
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_comisiones" id="email_comisiones"
                                   {{ ($configuraciones['notificaciones']['email_comisiones'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label config-form-label" for="email_comisiones">
                                Notificaciones de comisiones por email
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_nuevos_referidos" id="email_referidos"
                                   {{ ($configuraciones['notificaciones']['email_nuevos_referidos'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label config-form-label" for="email_referidos">
                                Notificaciones de nuevos referidos
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="sms_pedidos_entregados" id="sms_entregados"
                                   {{ ($configuraciones['notificaciones']['sms_pedidos_entregados'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label config-form-label" for="sms_entregados">
                                SMS cuando pedidos son entregados
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="whatsapp_recordatorios" id="whatsapp_recordatorios"
                                   {{ ($configuraciones['notificaciones']['whatsapp_recordatorios'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label config-form-label" for="whatsapp_recordatorios">
                                Recordatorios por WhatsApp
                            </label>
                        </div>
                        <div class="collapse" id="configNotif">
                            <div class="border-top pt-3 d-flex gap-2">
                                <button type="submit" class="config-action-btn-success">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Guardar Notificaciones</span>
                                </button>
                                <button type="button" class="config-action-btn-secondary" data-bs-toggle="collapse" data-bs-target="#configNotif">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Cancelar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Estado del Sistema --}}
        <div class="col-xl-6 mb-4">
            <div class="config-section-card animate-fadeInUp animate-delay-4">
                <div class="config-card-header">
                    <h5><i class="bi bi-shield-check-fill"></i>Estado del Sistema</h5>
                </div>
                <div class="config-card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <div class="config-status-indicator active">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <small class="d-block mt-2 text-muted fw-bold">Sistema Activo</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <div class="config-status-indicator {{ ($configuraciones['sistema']['backup_automatico'] ?? true) ? 'active' : 'inactive' }}">
                                    <i class="bi bi-{{ ($configuraciones['sistema']['backup_automatico'] ?? true) ? 'check-lg' : 'exclamation-lg' }}"></i>
                                </div>
                                <small class="d-block mt-2 text-muted fw-bold">Backups {{ ($configuraciones['sistema']['backup_automatico'] ?? true) ? 'Activos' : 'Inactivos' }}</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-0" style="color:var(--info)">{{ $configuraciones['sistema']['version'] ?? '1.0.0' }}</h4>
                                <small class="text-muted">Versión del Sistema</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-0" style="color:var(--wine)">{{ $configuraciones['sistema']['logs_dias_retention'] ?? 30 }}d</h4>
                                <small class="text-muted">Retención de Logs</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="config-action-btn-danger w-100" data-config-action="logs">
                            <i class="bi bi-trash-fill"></i>
                            <span>Limpiar Logs Antiguos (>30 días)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.configuracionRoutes = {
        backup: '{{ route("admin.configuracion.backup") }}',
        limpiarCache: '{{ route("admin.configuracion.limpiar-cache") }}',
        limpiarLogs: '{{ route("admin.configuracion.limpiar-logs") }}',
        infoSistema: '{{ route("admin.configuracion.info-sistema") }}'
    };
    window.configuracionCSRF = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/admin/configuracion-modern.js') }}?v={{ filemtime(public_path('js/admin/configuracion-modern.js')) }}"></script>
@endpush
