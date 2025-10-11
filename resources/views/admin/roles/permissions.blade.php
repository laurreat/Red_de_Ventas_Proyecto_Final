@extends('layouts.admin')

@section('title', '- Lista de Permisos')
@section('page-title', 'Lista de Permisos del Sistema')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/roles-modern.css') }}?v={{ filemtime(public_path('css/admin/roles-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="roles-header fade-in-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="roles-title">
                    <i class="bi bi-key me-2"></i>
                    Lista de Permisos del Sistema
                </h1>
                <p class="roles-subtitle">
                    Todos los permisos disponibles organizados por categorías
                </p>
            </div>
            <div class="roles-header-actions">
                <a href="{{ route('admin.roles.index') }}" class="roles-btn roles-btn-white">
                    <i class="bi bi-arrow-left"></i>
                    Volver a Roles
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Info --}}
    <div class="alert alert-info fade-in-up" style="border-radius: 12px; border-left: 4px solid var(--info); background: rgba(59, 130, 246, 0.1);">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Información:</strong> Estos son todos los permisos disponibles en el sistema. Los permisos se asignan a los roles para controlar el acceso a diferentes funcionalidades.
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="role-stat-card fade-in-up">
                <div class="role-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="bi bi-key"></i>
                </div>
                <div class="role-stat-value">{{ count($permissions) }}</div>
                <div class="role-stat-label">Total de Permisos</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="role-stat-card fade-in-up animate-delay-1">
                <div class="role-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div class="role-stat-value">{{ count($categories) }}</div>
                <div class="role-stat-label">Categorías</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="role-stat-card fade-in-up animate-delay-2">
                <div class="role-stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div class="role-stat-value">{{ count(array_filter($permissions, function($p) { return strpos($p['name'], 'admin.') === 0; })) }}</div>
                <div class="role-stat-label">Permisos de Admin</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="role-stat-card fade-in-up animate-delay-3">
                <div class="role-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="bi bi-shield"></i>
                </div>
                <div class="role-stat-value">{{ count(array_filter($permissions, function($p) { return strpos($p['name'], 'admin.') !== 0; })) }}</div>
                <div class="role-stat-label">Permisos Generales</div>
            </div>
        </div>
    </div>

    {{-- Permissions by Category --}}
    <div class="row">
        @foreach($categories as $categoryKey => $categoryName)
            @php
                $categoryPermissions = array_filter($permissions, function($permission) use ($categoryKey) {
                    return $permission['category'] === $categoryKey;
                });
            @endphp
            @if(!empty($categoryPermissions))
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="permission-category-card fade-in-up" style="animation-delay: {{ $loop->index * 0.05 }}s; opacity: 0; animation-fill-mode: forwards;">
                    <div class="permission-category-header">
                        <h6 class="permission-category-title">
                            <i class="bi bi-folder2-open text-primary"></i>
                            {{ $categoryName }}
                            <span class="role-badge role-badge-permissions ms-2">
                                {{ count($categoryPermissions) }}
                            </span>
                        </h6>
                    </div>
                    <div class="permission-category-body">
                        @foreach($categoryPermissions as $permission)
                        <div class="permission-item">
                            <div class="permission-icon">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div style="flex: 1;">
                                <div class="permission-name">
                                    {{ $permission['display_name'] }}
                                    @if(strpos($permission['name'], 'admin.') === 0)
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 0.688rem; padding: 0.25rem 0.5rem;">
                                            Admin
                                        </span>
                                    @endif
                                </div>
                                <code class="small text-muted d-block mb-1" style="font-size: 0.75rem;">{{ $permission['name'] }}</code>
                                @if($permission['description'])
                                    <div class="permission-description">{{ $permission['description'] }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>

    {{-- Category Summary --}}
    <div class="role-info-card fade-in-up">
        <div class="role-info-card-header">
            <i class="bi bi-list-ul"></i>
            <h3 class="role-info-card-title">Resumen por Categorías</h3>
        </div>
        <div class="role-info-card-body">
            <div class="row">
                @php
                    $halfCount = ceil(count($categories) / 2);
                @endphp
                <div class="col-md-6">
                    @foreach(array_slice($categories, 0, $halfCount, true) as $key => $name)
                    <div class="role-info-item">
                        <div>
                            <span class="role-info-label">{{ $name }}</span>
                            <br><code style="font-size: 0.75rem; color: var(--gray-500);">{{ $key }}</code>
                        </div>
                        <span class="role-badge role-badge-permissions">
                            {{ count(array_filter($permissions, function($p) use ($key) { return $p['category'] === $key; })) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                <div class="col-md-6">
                    @foreach(array_slice($categories, $halfCount, null, true) as $key => $name)
                    <div class="role-info-item">
                        <div>
                            <span class="role-info-label">{{ $name }}</span>
                            <br><code style="font-size: 0.75rem; color: var(--gray-500);">{{ $key }}</code>
                        </div>
                        <span class="role-badge role-badge-permissions">
                            {{ count(array_filter($permissions, function($p) use ($key) { return $p['category'] === $key; })) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="text-center mt-4 mb-4">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-primary btn-lg" style="border-radius: 12px; padding: 0.875rem 2rem;">
            <i class="bi bi-arrow-left me-2"></i>
            Volver a Gestión de Roles
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/roles-modern.js') }}?v={{ filemtime(public_path('js/admin/roles-modern.js')) }}"></script>
@endpush
