@extends('layouts.admin')

@section('title', '- Detalles del Usuario')
@section('page-title', 'Detalles del Usuario')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg me-3">
                        <div class="avatar-title rounded-circle d-flex align-items-center justify-content-center"
                             style="background: var(--primary-color); color: white; width: 60px; height: 60px; font-size: 24px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-0" style="color: var(--primary-color);">{{ $user->name }} {{ $user->apellidos }}</h4>
                        <p class="text-muted mb-0">
                            <span class="badge bg-{{ $user->activo ? 'success' : 'danger' }} me-2">
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                            {{ ucfirst($user->rol) }} • {{ $user->email }}
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary me-2">
                        <i class="bi bi-pencil me-1"></i>
                        Editar
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(13, 202, 240, 0.1);">
                        <i class="bi bi-bag fs-2 text-info"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-info">{{ $stats['pedidos_como_cliente'] }}</h3>
                    <p class="text-muted mb-0 small">Pedidos como Cliente</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-cart-check fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $stats['pedidos_como_vendedor'] }}</h3>
                    <p class="text-muted mb-0 small">Pedidos como Vendedor</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-currency-dollar fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">${{ number_format($stats['total_vendido'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Total Vendido</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-gem fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${{ number_format($stats['comisiones_totales'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Comisiones Totales</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Personal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-person me-2"></i>
                        Información Personal
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small">Nombres</label>
                            <div class="fw-semibold">{{ $user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Apellidos</label>
                            <div class="fw-semibold">{{ $user->apellidos }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Cédula</label>
                            <div class="fw-semibold">{{ $user->cedula }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Fecha de Nacimiento</label>
                            <div class="fw-semibold">
                                {{ $user->fecha_nacimiento ? $user->fecha_nacimiento->format('d/m/Y') : 'No especificada' }}
                                @if($user->fecha_nacimiento)
                                    <small class="text-muted">({{ $user->fecha_nacimiento->age }} años)</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <div class="fw-semibold">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Teléfono</label>
                            <div class="fw-semibold">{{ $user->telefono }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Ciudad</label>
                            <div class="fw-semibold">{{ $user->ciudad }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Departamento</label>
                            <div class="fw-semibold">{{ $user->departamento }}</div>
                        </div>
                        @if($user->direccion)
                            <div class="col-12">
                                <label class="text-muted small">Dirección</label>
                                <div class="fw-semibold">{{ $user->direccion }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Red MLM -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Red de Marketing Multinivel
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        @if($user->referidor)
                            <div class="col-md-6">
                                <label class="text-muted small">Referido por</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="avatar-title rounded-circle d-flex align-items-center justify-content-center"
                                             style="background: var(--primary-color); color: white; font-size: 12px;">
                                            {{ strtoupper(substr($user->referidor->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->referidor->name }} {{ $user->referidor->apellidos }}</div>
                                        <small class="text-muted">{{ ucfirst($user->referidor->rol) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label class="text-muted small">Código de Referido</label>
                            <div class="fw-semibold">
                                <span class="badge bg-primary">{{ $user->codigo_referido }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Total de Referidos</label>
                            <div class="fw-semibold">{{ $user->total_referidos ?? 0 }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Meta Mensual</label>
                            <div class="fw-semibold">
                                ${{ number_format($user->meta_mensual ?? 0, 2) }}
                            </div>
                        </div>
                    </div>

                    @if($user->referidos->count() > 0)
                        <hr>
                        <h6 class="fw-semibold mb-3">Referidos Directos ({{ $user->referidos->count() }})</h6>
                        <div class="row g-2">
                            @foreach($user->referidos->take(6) as $referido)
                                <div class="col-lg-4 col-md-6">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title rounded-circle d-flex align-items-center justify-content-center"
                                                 style="background: var(--primary-color); color: white; font-size: 12px;">
                                                {{ strtoupper(substr($referido->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">{{ $referido->name }} {{ $referido->apellidos }}</div>
                                            <small class="text-muted">{{ ucfirst($referido->rol) }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($user->referidos->count() > 6)
                            <div class="text-center mt-3">
                                <small class="text-muted">Y {{ $user->referidos->count() - 6 }} más...</small>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-lg-4">
            <!-- Configuración del Sistema -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-gear me-2"></i>
                        Configuración del Sistema
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small">Rol</label>
                        <div>
                            @php
                                $roleColors = [
                                    'administrador' => 'success',
                                    'lider' => 'info',
                                    'vendedor' => 'warning',
                                    'cliente' => 'secondary'
                                ];
                            @endphp
                            <span class="badge bg-{{ $roleColors[$user->rol] ?? 'secondary' }}">
                                {{ ucfirst($user->rol) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Estado</label>
                        <div>
                            <span class="badge bg-{{ $user->activo ? 'success' : 'danger' }}">
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Fecha de Registro</label>
                        <div class="fw-semibold">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Última Actualización</label>
                        <div class="fw-semibold">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Financieras -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-graph-up me-2"></i>
                        Estadísticas Financieras
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small">Ventas Mes Actual</label>
                        <div class="fw-semibold">${{ number_format($user->ventas_mes_actual ?? 0, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Comisiones Ganadas</label>
                        <div class="fw-semibold text-success">${{ number_format($user->comisiones_ganadas ?? 0, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Comisiones Disponibles</label>
                        <div class="fw-semibold text-info">${{ number_format($user->comisiones_disponibles ?? 0, 2) }}</div>
                    </div>
                    <div>
                        <label class="text-muted small">Total Gastado</label>
                        <div class="fw-semibold">${{ number_format($stats['total_gastado'], 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i>
                            Editar Usuario
                        </a>
                        <button type="button"
                                class="btn btn-outline-{{ $user->activo ? 'warning' : 'success' }} w-100"
                                onclick="event.preventDefault(); toggleUserStatus('{{ $user->_id }}'); return false;">
                            <i class="bi bi-{{ $user->activo ? 'pause' : 'play' }} me-1"></i>
                            {{ $user->activo ? 'Desactivar' : 'Activar' }}
                        </button>

                        <!-- Formulario oculto -->
                        <form id="user-toggle-form-{{ $user->_id }}"
                              action="{{ route('admin.users.toggle-active', $user) }}"
                              method="POST" class="d-none">
                            @csrf
                            @method('PATCH')
                        </form>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-lg {
    width: 60px;
    height: 60px;
}

.avatar-title {
    font-weight: 600;
}
</style>
@endsection

{{-- Incluir modales de confirmación para usuarios --}}
@include('admin.partials.modals-users')

@push('scripts')
<script>
// Funciones específicas para la vista de detalles del usuario
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        console.log('Inicializando funciones para vista show de usuario...');

        // Función para cambiar estado de usuario
        window.toggleUserStatus = function(userId) {
            console.log('Show toggleUserStatus ejecutada para usuario:', userId);

            // Obtener información del usuario de PHP
            const isActive = {{ $user->activo ? 'true' : 'false' }};
            const userName = '{{ $user->name }} {{ $user->apellidos }}';
            const userEmail = '{{ $user->email }}';
            const userRole = '{{ ucfirst($user->rol) }}';

            // Configurar modal dinámicamente
            const statusHeader = document.getElementById('userStatusModalHeader');
            const statusIcon = document.getElementById('userStatusIcon');
            const statusIconContainer = document.getElementById('userStatusIconContainer');
            const statusTitle = document.getElementById('userStatusTitle');
            const statusMessage = document.getElementById('userStatusMessage');
            const statusBtn = document.getElementById('confirmUserStatusBtn');
            const statusBtnText = document.getElementById('userStatusBtnText');
            const statusBtnIcon = document.getElementById('userStatusBtnIcon');

            // Actualizar información del usuario en el modal
            const userNameEl = document.getElementById('userStatusName');
            const userEmailEl = document.getElementById('userStatusEmail');
            const userRoleEl = document.getElementById('userStatusRole');
            const userAvatar = document.getElementById('userAvatar');

            if (userNameEl) userNameEl.textContent = userName;
            if (userEmailEl) userEmailEl.textContent = userEmail;
            if (userRoleEl) userRoleEl.textContent = userRole;
            if (userAvatar) userAvatar.textContent = userName.charAt(0).toUpperCase();

            if (isActive) {
                // Desactivar usuario
                statusHeader.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
                statusIconContainer.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
                statusIcon.className = 'bi bi-person-x-fill text-warning fs-1';
                statusTitle.textContent = '¿Deseas desactivar este usuario?';
                statusMessage.textContent = 'El usuario no podrá acceder al sistema y se suspenderán sus permisos.';
                statusBtn.className = 'btn btn-warning';
                statusBtnIcon.className = 'bi bi-person-x me-1';
                statusBtnText.textContent = 'Desactivar Usuario';
            } else {
                // Activar usuario
                statusHeader.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
                statusIconContainer.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
                statusIcon.className = 'bi bi-person-check-fill text-success fs-1';
                statusTitle.textContent = '¿Deseas activar este usuario?';
                statusMessage.textContent = 'El usuario podrá acceder al sistema y tendrá todos sus permisos habilitados.';
                statusBtn.className = 'btn btn-success';
                statusBtnIcon.className = 'bi bi-person-check me-1';
                statusBtnText.textContent = 'Activar Usuario';
            }

            // Configurar botón de confirmación
            statusBtn.onclick = function() {
                document.getElementById(`user-toggle-form-${userId}`).submit();
            };

            // Mostrar modal
            const modalElement = document.getElementById('userStatusConfirmModal');
            if (modalElement) {
                console.log('Mostrando modal de estado para usuario en vista show');
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');

                // Crear backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        };

        // Función para cerrar modales
        window.closeUserModal = function(modalId) {
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.classList.remove('modal-open');

                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        };

        // Event listeners para cerrar modales
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) closeUserModal(modal.id);
            });
        });

        // Cerrar con backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                const openModal = document.querySelector('.modal.show');
                if (openModal) closeUserModal(openModal.id);
            }
        });

        console.log('Funciones de vista show usuario inicializadas correctamente');
    }, 1000);
});
</script>
@endpush