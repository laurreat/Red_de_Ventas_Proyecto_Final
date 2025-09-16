@extends('layouts.admin')

@section('title', '- Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Administra todos los usuarios del sistema</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>
                        Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-people me-2"></i>
                        Lista de Usuarios
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center py-5">
                        <i class="bi bi-gear fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Funcionalidad en Desarrollo</h4>
                        <p class="text-muted">El CRUD completo de usuarios estará disponible próximamente.</p>
                        <p class="text-muted">Por ahora puedes gestionar usuarios desde el dashboard principal.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection