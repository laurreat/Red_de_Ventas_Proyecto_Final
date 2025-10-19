{{--
    Header Unificado para todos los roles (Admin, Líder, Vendedor)
    Compatible con Bootstrap 5 y diseño moderno consistente
--}}
@php
    $headerClass = 'admin-header';
    if(Auth::check()) {
        if(Auth::user()->esLider()) {
            $headerClass = 'lider-header';
        } elseif(!Auth::user()->esAdmin()) {
            $headerClass = 'vendedor-header';
        }
    }
@endphp

<header class="{{ $headerClass }}" id="{{ $headerId ?? 'adminHeader' }}">
    <div class="header-content">
        <div class="header-left">
            <button class="sidebar-toggle" id="{{ $sidebarToggleId ?? 'sidebarToggle' }}">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="header-title">@yield('page-title', 'Dashboard')</h1>
        </div>

        <div class="header-right">
            <!-- Notifications -->
            <div class="dropdown">
                <button class="header-notifications" data-bs-toggle="dropdown" aria-expanded="false" id="notificationsDropdown">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge-animated" id="notificationBadge" style="display: none;">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end header-dropdown-menu notifications-dropdown" id="notificationsDropdownMenu">
                    <!-- Header -->
                    <div class="dropdown-header-modern">
                        <h6>
                            <i class="bi bi-bell me-2"></i>
                            Notificaciones
                        </h6>
                        <div class="dropdown-header-actions">
                            <span class="notification-count-badge" id="notificationCount">0 nuevas</span>
                            <button class="btn btn-view-all" onclick="verTodasLasNotificaciones()">
                                Ver todas
                            </button>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="notifications-list" id="notificationsList">
                        <div class="notifications-empty">
                            <i class="bi bi-bell-slash"></i>
                            <h6>Sin notificaciones</h6>
                            <p>No tienes notificaciones nuevas en este momento</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="dropdown-footer">
                        <button class="btn btn-mark-all-read" onclick="marcarTodasLeidasDropdown()">
                            <i class="bi bi-check-all"></i>
                            Marcar todas como leídas
                        </button>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="dropdown">
                <div class="header-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-avatar">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}"
                                 alt="Avatar">
                        @else
                            <div class="avatar-placeholder">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="profile-info">
                        <div class="profile-name">{{ Auth::user()->name }}</div>
                        <div class="profile-role">{{ ucfirst(Auth::user()->rol) }}</div>
                    </div>
                    <i class="bi bi-chevron-down ms-2"></i>
                </div>
                <div class="dropdown-menu dropdown-menu-end header-dropdown-menu profile-dropdown">
                    <!-- Profile Header -->
                    <div class="profile-dropdown-header">
                        <div class="profile-dropdown-avatar">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}"
                                     alt="Avatar">
                            @else
                                <div class="avatar-initial">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="profile-dropdown-name">{{ Auth::user()->name }}</div>
                        <div class="profile-dropdown-email">{{ Auth::user()->email }}</div>
                        <span class="profile-dropdown-role">
                            <i class="bi bi-shield-check me-1"></i>
                            {{ ucfirst(Auth::user()->rol) }}
                        </span>
                    </div>

                    <!-- Stats Section -->
                    @php
                        // Stats personalizados según el rol
                        $stats = [];
                        try {
                            if(Auth::user()->esAdmin()) {
                                $totalVentas = 0;
                                $pedidos = \App\Models\Pedido::all();
                                foreach($pedidos as $pedido) {
                                    $totalVentas += to_float($pedido->total_final ?? 0);
                                }
                                $stats = [
                                    'pedidos' => \App\Models\Pedido::count(),
                                    'usuarios' => \App\Models\User::count(),
                                    'ventas' => $totalVentas
                                ];
                            } elseif(Auth::user()->esLider()) {
                                $stats = [
                                    'ventas' => \App\Models\Pedido::where('vendedor_id', Auth::id())->count(),
                                    'equipo' => \App\Models\User::where('referido_por', Auth::id())->count(),
                                    'comisiones' => to_float(Auth::user()->comisiones_disponibles ?? 0)
                                ];
                            } else {
                                // Stats para vendedor
                                $totalComisiones = 0;
                                $comisiones = \App\Models\Comision::where('user_id', Auth::id())->get();
                                foreach($comisiones as $comision) {
                                    // El campo correcto es 'monto' no 'monto_comision'
                                    $totalComisiones += to_float($comision->monto ?? 0);
                                }

                                // Contar clientes únicos manualmente para MongoDB
                                $pedidos = \App\Models\Pedido::where('vendedor_id', Auth::id())->get();
                                $clientesUnicos = [];
                                foreach($pedidos as $pedido) {
                                    // Los clientes están en cliente_data o user_id
                                    $clienteId = $pedido->user_id ??
                                                ($pedido->cliente_data['_id'] ?? null) ??
                                                ($pedido->cliente_data['email'] ?? null);
                                    if($clienteId && !in_array($clienteId, $clientesUnicos)) {
                                        $clientesUnicos[] = $clienteId;
                                    }
                                }

                                $stats = [
                                    'ventas' => $pedidos->count(),
                                    'clientes' => count($clientesUnicos),
                                    'comisiones' => $totalComisiones
                                ];
                            }
                        } catch(\Exception $e) {
                            $stats = [];
                        }
                    @endphp
                    @if(!empty($stats))
                    <div class="profile-stats">
                        @if(Auth::user()->esAdmin())
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $stats['pedidos'] }}</span>
                                <span class="profile-stat-label">Pedidos</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $stats['usuarios'] }}</span>
                                <span class="profile-stat-label">Usuarios</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">${{ format_currency($stats['ventas']) }}</span>
                                <span class="profile-stat-label">Ventas</span>
                            </div>
                        @elseif(Auth::user()->esLider())
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $stats['ventas'] }}</span>
                                <span class="profile-stat-label">Ventas</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $stats['equipo'] }}</span>
                                <span class="profile-stat-label">Equipo</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">${{ format_currency($stats['comisiones']) }}</span>
                                <span class="profile-stat-label">Comisiones</span>
                            </div>
                        @else
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $stats['ventas'] }}</span>
                                <span class="profile-stat-label">Ventas</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $stats['clientes'] }}</span>
                                <span class="profile-stat-label">Clientes</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">${{ format_currency($stats['comisiones']) }}</span>
                                <span class="profile-stat-label">Comisiones</span>
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Menu Items -->
                    <div class="profile-menu-section">
                        @if(Auth::user()->esAdmin())
                            <a href="{{ route('admin.perfil.index') }}" class="profile-menu-item">
                                <i class="bi bi-person"></i>
                                <span class="menu-item-text">Mi Perfil</span>
                            </a>
                            <a href="{{ route('admin.configuracion.index') }}" class="profile-menu-item">
                                <i class="bi bi-gear"></i>
                                <span class="menu-item-text">Configuración</span>
                            </a>
                            <a href="{{ route('admin.notificaciones.index') }}" class="profile-menu-item">
                                <i class="bi bi-bell"></i>
                                <span class="menu-item-text">Notificaciones</span>
                                @php
                                    $unreadCount = \App\Models\Notificacion::where('user_id', Auth::id())
                                        ->where('leida', false)
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="menu-item-badge">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        @elseif(Auth::user()->esLider())
                            <a href="{{ route('lider.perfil.index') }}" class="profile-menu-item">
                                <i class="bi bi-person"></i>
                                <span class="menu-item-text">Mi Perfil</span>
                            </a>
                            <a href="{{ route('lider.configuracion.index') }}" class="profile-menu-item">
                                <i class="bi bi-gear"></i>
                                <span class="menu-item-text">Configuración</span>
                            </a>
                            <a href="{{ route('lider.notificaciones.index') }}" class="profile-menu-item">
                                <i class="bi bi-bell"></i>
                                <span class="menu-item-text">Notificaciones</span>
                                @php
                                    $unreadCount = \App\Models\Notificacion::where('user_id', Auth::id())
                                        ->where('leida', false)
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="menu-item-badge">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        @else
                            <a href="{{ route('vendedor.perfil.index') }}" class="profile-menu-item">
                                <i class="bi bi-person"></i>
                                <span class="menu-item-text">Mi Perfil</span>
                            </a>
                            <a href="{{ route('vendedor.notificaciones.index') }}" class="profile-menu-item">
                                <i class="bi bi-bell"></i>
                                <span class="menu-item-text">Notificaciones</span>
                                @php
                                    $unreadCount = \App\Models\Notificacion::where('user_id', Auth::id())
                                        ->where('leida', false)
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="menu-item-badge">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        @endif
                    </div>

                    <hr class="profile-menu-divider">

                    <div class="profile-menu-section">
                        @if(Auth::user()->esAdmin())
                            <a href="{{ route('admin.ayuda.index') }}" class="profile-menu-item">
                                <i class="bi bi-question-circle"></i>
                                <span class="menu-item-text">Ayuda</span>
                            </a>
                        @elseif(Auth::user()->esLider())
                            <a href="{{ route('lider.ayuda.index') }}" class="profile-menu-item">
                                <i class="bi bi-question-circle"></i>
                                <span class="menu-item-text">Ayuda</span>
                            </a>
                        @else
                            <a href="{{ route('vendedor.ayuda.index') }}" class="profile-menu-item">
                                <i class="bi bi-question-circle"></i>
                                <span class="menu-item-text">Ayuda</span>
                            </a>
                        @endif
                    </div>

                    <hr class="profile-menu-divider">

                    <div class="profile-menu-section">
                        <a href="{{ route('logout') }}" class="profile-menu-item danger"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="menu-item-text">Cerrar Sesión</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Función para redirigir a la página de notificaciones según el rol
function verTodasLasNotificaciones() {
    @if(Auth::check())
        @if(Auth::user()->esAdmin())
            window.location.href = '{{ route('admin.notificaciones.index') }}';
        @elseif(Auth::user()->esLider())
            window.location.href = '{{ route('lider.notificaciones.index') }}';
        @else
            window.location.href = '{{ route('vendedor.notificaciones.index') }}';
        @endif
    @endif
}

// Función para marcar todas como leídas desde el dropdown
function marcarTodasLeidasDropdown() {
    @if(Auth::check())
        @if(Auth::user()->esAdmin())
            const url = '{{ route('admin.notificaciones.marcar-todas-leidas') }}';
        @elseif(Auth::user()->esLider())
            const url = '{{ route('lider.notificaciones.marcar-todas-leidas') }}';
        @else
            const url = '{{ route('vendedor.notificaciones.marcar-todas-leidas') }}';
        @endif

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                // Actualizar el badge
                const badge = document.getElementById('notificationBadge');
                if(badge) {
                    badge.style.display = 'none';
                    badge.textContent = '0';
                }
                // Actualizar contador
                const count = document.getElementById('notificationCount');
                if(count) {
                    count.textContent = '0 nuevas';
                }
                // Limpiar lista
                const list = document.getElementById('notificationsList');
                if(list) {
                    list.innerHTML = '<div class="notifications-empty"><i class="bi bi-bell-slash"></i><h6>Sin notificaciones</h6><p>No tienes notificaciones nuevas en este momento</p></div>';
                }
            }
        })
        .catch(err => console.error('Error:', err));
    @endif
}
</script>
