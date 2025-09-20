@extends('layouts.admin')

@section('title', 'Lista de Permisos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Lista de Permisos del Sistema</h3>
                    <div>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver a Roles
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Información:</strong> Estos son todos los permisos disponibles en el sistema.
                        Los permisos se asignan a los roles para controlar el acceso a diferentes funcionalidades.
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ count($permissions) }}</h4>
                                    <p class="mb-0">Total Permisos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>{{ count($categories) }}</h4>
                                    <p class="mb-0">Categorías</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ count(array_filter($permissions, function($p) { return strpos($p['name'], 'admin.') === 0; })) }}</h4>
                                    <p class="mb-0">Permisos Admin</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>{{ count(array_filter($permissions, function($p) { return strpos($p['name'], 'admin.') !== 0; })) }}</h4>
                                    <p class="mb-0">Permisos Generales</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @foreach($categories as $categoryKey => $categoryName)
                            @php
                                $categoryPermissions = array_filter($permissions, function($permission) use ($categoryKey) {
                                    return $permission['category'] === $categoryKey;
                                });
                            @endphp
                            @if(!empty($categoryPermissions))
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-folder2-open text-primary me-2"></i>
                                            {{ $categoryName }}
                                            <span class="badge bg-secondary">{{ count($categoryPermissions) }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach($categoryPermissions as $permission)
                                        <div class="border-bottom pb-2 mb-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $permission['display_name'] }}</h6>
                                                    <code class="small text-muted">{{ $permission['name'] }}</code>
                                                    @if($permission['description'])
                                                        <p class="small text-muted mb-0 mt-1">{{ $permission['description'] }}</p>
                                                    @endif
                                                </div>
                                                <span class="badge bg-{{ strpos($permission['name'], 'admin.') === 0 ? 'danger' : 'primary' }} ms-2">
                                                    {{ strpos($permission['name'], 'admin.') === 0 ? 'Admin' : 'General' }}
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <h5>Descripción de Categorías</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    @foreach(array_slice($categories, 0, ceil(count($categories) / 2), true) as $key => $name)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $name }}</strong>
                                            <small class="text-muted d-block">{{ $key }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">
                                            {{ count(array_filter($permissions, function($p) use ($key) { return $p['category'] === $key; })) }}
                                        </span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    @foreach(array_slice($categories, ceil(count($categories) / 2), null, true) as $key => $name)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $name }}</strong>
                                            <small class="text-muted d-block">{{ $key }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">
                                            {{ count(array_filter($permissions, function($p) use ($key) { return $p['category'] === $key; })) }}
                                        </span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Los permisos están organizados por categorías para facilitar su gestión.
                        </small>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Volver a Roles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection