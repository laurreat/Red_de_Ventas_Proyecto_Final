@extends('layouts.admin')

@section('title', '- Crear Usuario')
@section('page-title', 'Crear Usuario')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Agregar nuevo usuario al sistema</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-person-plus me-2"></i>
                        Información del Usuario
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center py-5">
                        <i class="bi bi-gear fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Funcionalidad en Desarrollo</h4>
                        <p class="text-muted">El formulario de creación de usuarios estará disponible próximamente.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection