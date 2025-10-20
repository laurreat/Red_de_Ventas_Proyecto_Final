@extends('layouts.cliente')

@section('title', '- Mi Perfil')
@section('header-title', 'Mi Perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cliente/perfil-modern.css') }}?v={{ filemtime(public_path('css/cliente/perfil-modern.css')) }}">
@endpush

@section('content')
<div class="profile-container fade-in-up">
    
    {{-- Banner Superior --}}
    <div class="profile-banner">
        <div class="profile-banner-overlay"></div>
        <div class="profile-banner-pattern"></div>
    </div>

    <div class="profile-main">
        {{-- Header con Avatar --}}
        <div class="profile-header">
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar-container">
                    @if($user->foto)
                        <img src="{{ Storage::url($user->foto) }}" alt="{{ $user->name }}" class="profile-avatar" loading="lazy">
                    @else
                        <div class="profile-avatar profile-avatar-placeholder">
                            <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    @endif
                    <div class="profile-avatar-status"></div>
                    <button class="profile-avatar-edit" onclick="document.getElementById('fotoInput').click()" title="Cambiar foto">
                        <i class="bi bi-camera"></i>
                    </button>
                    <form action="{{ route('cliente.perfil.actualizar-foto') }}" method="POST" enctype="multipart/form-data" id="fotoForm">
                        @csrf
                        <input type="file" name="foto" id="fotoInput" accept="image/*" style="display:none" onchange="handleAvatarUpload(this)">
                    </form>
                </div>
            </div>
            
            <div class="profile-info">
                <div class="profile-name-section">
                    <h1 class="profile-name">{{ $user->name }} {{ $user->apellidos ?? '' }}</h1>
                    @if($user->email_verified_at)
                    <span class="profile-badge profile-badge-verified">
                        <i class="bi bi-patch-check-fill"></i> Verificado
                    </span>
                    @endif
                </div>
                
                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <i class="bi bi-envelope-fill"></i>
                        <span>{{ $user->email }}</span>
                    </div>
                    @if($user->telefono)
                    <div class="profile-meta-item">
                        <i class="bi bi-phone-fill"></i>
                        <span>{{ $user->telefono }}</span>
                    </div>
                    @endif
                    @if($user->ciudad)
                    <div class="profile-meta-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>{{ $user->ciudad }}</span>
                    </div>
                    @endif
                    <div class="profile-meta-item">
                        <i class="bi bi-calendar-check-fill"></i>
                        <span>Cliente desde {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <button onclick="showEditModal()" class="btn-profile btn-primary">
                        <i class="bi bi-pencil-square"></i>
                        <span>Editar Perfil</span>
                    </button>
                    <button onclick="showPasswordModal()" class="btn-profile btn-secondary">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Cambiar Contraseña</span>
                    </button>
                    @if($user->foto)
                    <form action="{{ route('cliente.perfil.eliminar-foto') }}" method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar foto de perfil?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-profile btn-outline">
                            <i class="bi bi-trash"></i>
                            <span>Eliminar Foto</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>


        {{-- Tarjetas de Estadísticas --}}
        <div class="profile-stats-grid">
            <div class="stat-card stat-card-primary animate-delay-1 fade-in-up">
                <div class="stat-icon">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['total_pedidos'] ?? 0 }}</div>
                    <div class="stat-label">Pedidos Realizados</div>
                    <div class="stat-trend">
                        <i class="bi bi-box-seam"></i>
                        <span>Total de compras</span>
                    </div>
                </div>
            </div>
            
            <div class="stat-card stat-card-success animate-delay-2 fade-in-up">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['pedidos_entregados'] ?? 0 }}</div>
                    <div class="stat-label">Pedidos Entregados</div>
                    <div class="stat-trend stat-trend-up">
                        <i class="bi bi-arrow-up"></i>
                        <span>Completados exitosamente</span>
                    </div>
                </div>
            </div>
            
            <div class="stat-card stat-card-warning animate-delay-3 fade-in-up">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">${{ number_format($stats['total_gastado'] ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Gastado</div>
                    <div class="stat-trend">
                        <i class="bi bi-cart-check"></i>
                        <span>En todas tus compras</span>
                    </div>
                </div>
            </div>
            
            <div class="stat-card stat-card-info animate-delay-3 fade-in-up">
                <div class="stat-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['fecha_registro']->diffInDays(now()) }}</div>
                    <div class="stat-label">Días Como Cliente</div>
                    <div class="stat-trend">
                        <i class="bi bi-clock-history"></i>
                        <span>Desde {{ $stats['fecha_registro']->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pestañas de Navegación --}}
        <div class="profile-tabs">
            <button class="profile-tab active" data-tab="personal">
                <i class="bi bi-person-fill"></i>
                <span>Información Personal</span>
            </button>
            <button class="profile-tab" data-tab="direcciones">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Direcciones</span>
            </button>
            <button class="profile-tab" data-tab="pedidos">
                <i class="bi bi-bag-fill"></i>
                <span>Mis Pedidos</span>
            </button>
            <button class="profile-tab" data-tab="favoritos">
                <i class="bi bi-heart-fill"></i>
                <span>Favoritos</span>
            </button>
            <button class="profile-tab" data-tab="settings">
                <i class="bi bi-gear-fill"></i>
                <span>Configuración</span>
            </button>
        </div>

        {{-- Contenido de las Pestañas --}}
        <div class="profile-tab-content-wrapper">
            
            {{-- Tab: Información Personal --}}
            <div class="profile-tab-content active" id="personal">
                <div class="content-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-person-badge-fill"></i> Datos Personales</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label class="info-label">Nombre Completo</label>
                                    <div class="info-value">{{ $user->name }} {{ $user->apellidos ?? '' }}</div>
                                </div>
                                @if($user->cedula)
                                <div class="info-item">
                                    <label class="info-label">Cédula</label>
                                    <div class="info-value">{{ $user->cedula }}</div>
                                </div>
                                @endif
                                <div class="info-item">
                                    <label class="info-label">Email</label>
                                    <div class="info-value">{{ $user->email }}</div>
                                </div>
                                @if($user->telefono)
                                <div class="info-item">
                                    <label class="info-label">Teléfono</label>
                                    <div class="info-value">{{ $user->telefono }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-geo-fill"></i> Ubicación</h3>
                        </div>
                        <div class="card-body">
                            @if($user->direccion || $user->ciudad)
                            <div class="info-grid">
                                @if($user->direccion)
                                <div class="info-item-full">
                                    <label class="info-label">Dirección</label>
                                    <div class="info-value">{{ $user->direccion }}</div>
                                </div>
                                @endif
                                @if($user->ciudad)
                                <div class="info-item">
                                    <label class="info-label">Ciudad</label>
                                    <div class="info-value">{{ $user->ciudad }}</div>
                                </div>
                                @endif
                                @if($user->departamento)
                                <div class="info-item">
                                    <label class="info-label">Departamento</label>
                                    <div class="info-value">{{ $user->departamento }}</div>
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="empty-state-small">
                                <i class="bi bi-geo"></i>
                                <p>No has registrado una dirección</p>
                                <button onclick="showEditModal()" class="btn-sm btn-primary">Agregar Dirección</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab: Direcciones --}}
            <div class="profile-tab-content" id="direcciones">
                <div class="card">
                    <div class="card-header card-header-flex">
                        <h3><i class="bi bi-house-fill"></i> Mis Direcciones</h3>
                        <button onclick="showEditModal()" class="btn-icon">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        @if($user->direccion)
                        <div class="address-card address-primary">
                            <div class="address-icon">
                                <i class="bi bi-house-door-fill"></i>
                            </div>
                            <div class="address-content">
                                <h4>Dirección Principal</h4>
                                <p>{{ $user->direccion }}</p>
                                <div class="address-meta">
                                    @if($user->ciudad)
                                    <span><i class="bi bi-geo-alt"></i> {{ $user->ciudad }}{{ $user->departamento ? ', '.$user->departamento : '' }}</span>
                                    @endif
                                    @if($user->telefono)
                                    <span><i class="bi bi-phone"></i> {{ $user->telefono }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="address-badge">
                                <span class="badge badge-primary">Principal</span>
                            </div>
                        </div>
                        @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-house-door"></i>
                            </div>
                            <h4>No tienes direcciones guardadas</h4>
                            <p>Agrega una dirección para facilitar tus compras</p>
                            <button onclick="showEditModal()" class="btn-primary">
                                <i class="bi bi-plus-circle"></i> Agregar Dirección
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tab: Mis Pedidos --}}
            <div class="profile-tab-content" id="pedidos">
                <div class="card">
                    <div class="card-header card-header-flex">
                        <h3><i class="bi bi-bag-check-fill"></i> Historial de Pedidos</h3>
                        @if(Route::has('cliente.pedidos.index'))
                        <a href="{{ route('cliente.pedidos.index') }}" class="btn-secondary btn-sm">
                            Ver Todos
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="quick-stats">
                            <div class="quick-stat">
                                <div class="quick-stat-icon stat-primary">
                                    <i class="bi bi-box2"></i>
                                </div>
                                <div class="quick-stat-content">
                                    <span class="quick-stat-value">{{ $stats['total_pedidos'] ?? 0 }}</span>
                                    <span class="quick-stat-label">Total Pedidos</span>
                                </div>
                            </div>
                            <div class="quick-stat">
                                <div class="quick-stat-icon stat-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="quick-stat-content">
                                    <span class="quick-stat-value">{{ $stats['pedidos_entregados'] ?? 0 }}</span>
                                    <span class="quick-stat-label">Entregados</span>
                                </div>
                            </div>
                            <div class="quick-stat">
                                <div class="quick-stat-icon stat-warning">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <div class="quick-stat-content">
                                    <span class="quick-stat-value">${{ number_format($stats['total_gastado'] ?? 0, 0, ',', '.') }}</span>
                                    <span class="quick-stat-label">Total Gastado</span>
                                </div>
                            </div>
                        </div>
                        @if(Route::has('cliente.pedidos.index'))
                        <div class="text-center mt-4">
                            <a href="{{ route('cliente.pedidos.index') }}" class="btn-primary">
                                <i class="bi bi-list-ul"></i> Ver Historial Completo
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tab: Favoritos --}}
            <div class="profile-tab-content" id="favoritos">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="bi bi-heart-fill"></i> Productos Favoritos</h3>
                    </div>
                    <div class="card-body">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-heart"></i>
                            </div>
                            <h4>Aún no tienes favoritos</h4>
                            <p>Marca tus productos preferidos para encontrarlos fácilmente</p>
                            @if(Route::has('cliente.dashboard'))
                            <a href="{{ route('cliente.dashboard') }}" class="btn-primary">
                                <i class="bi bi-shop"></i> Explorar Productos
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab: Configuración --}}
            <div class="profile-tab-content" id="settings">
                <div class="content-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-lock-fill"></i> Seguridad de la Cuenta</h3>
                            <p class="card-subtitle">Mantén tu cuenta protegida</p>
                        </div>
                        <div class="card-body">
                            <div class="settings-list">
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Contraseña</h4>
                                        <p>Última actualización: {{ $user->updated_at->diffForHumans() }}</p>
                                    </div>
                                    <button onclick="showPasswordModal()" class="btn-secondary">
                                        <i class="bi bi-key-fill"></i> Cambiar
                                    </button>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Email</h4>
                                        <p>{{ $user->email }}</p>
                                    </div>
                                    @if($user->email_verified_at)
                                    <span class="badge badge-success">
                                        <i class="bi bi-check-circle"></i> Verificado
                                    </span>
                                    @else
                                    <button class="btn-warning btn-sm">
                                        <i class="bi bi-exclamation-circle"></i> Verificar
                                    </button>
                                    @endif
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Cuenta Creada</h4>
                                        <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-bell-fill"></i> Preferencias</h3>
                        </div>
                        <div class="card-body">
                            <div class="settings-list">
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Notificaciones de Pedidos</h4>
                                        <p>Recibe actualizaciones sobre tus pedidos</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="switch-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Ofertas y Promociones</h4>
                                        <p>Entérate de descuentos especiales</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Editar Información --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="bi bi-pencil-square"></i> Editar Información Personal</h3>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('cliente.perfil.actualizar-informacion') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person"></i> Nombre *
                        </label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person"></i> Apellidos
                        </label>
                        <input type="text" name="apellidos" class="form-control" value="{{ $user->apellidos }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-phone"></i> Teléfono
                        </label>
                        <input type="text" name="telefono" class="form-control" value="{{ $user->telefono }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-card-text"></i> Cédula
                        </label>
                        <input type="text" name="cedula" class="form-control" value="{{ $user->cedula }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-house"></i> Dirección
                    </label>
                    <input type="text" name="direccion" class="form-control" value="{{ $user->direccion }}">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-geo-alt"></i> Ciudad
                        </label>
                        <input type="text" name="ciudad" class="form-control" value="{{ $user->ciudad }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-map"></i> Departamento
                        </label>
                        <input type="text" name="departamento" class="form-control" value="{{ $user->departamento }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check-circle-fill"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Cambiar Contraseña --}}
<div class="modal-overlay" id="passwordModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="bi bi-shield-lock-fill"></i> Cambiar Contraseña</h3>
            <button class="modal-close" onclick="closePasswordModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('cliente.perfil.cambiar-password') }}" method="POST" id="passwordForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-lock"></i> Contraseña Actual *
                    </label>
                    <input type="password" name="password_actual" class="form-control" required placeholder="Ingresa tu contraseña actual">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-key"></i> Nueva Contraseña *
                    </label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Mínimo 8 caracteres">
                    <small class="form-hint">Usa al menos 8 caracteres con mayúsculas, minúsculas y números</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-key-fill"></i> Confirmar Nueva Contraseña *
                    </label>
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="Repite la nueva contraseña">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closePasswordModal()">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check-circle-fill"></i> Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Notifications --}}
<div id="toastContainer"></div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast('{{ session('success') }}', 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast('{{ session('error') }}', 'error');
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'error');
        @endforeach
    });
</script>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/cliente/perfil-modern.js') }}?v={{ filemtime(public_path('js/cliente/perfil-modern.js')) }}"></script>
@endpush
