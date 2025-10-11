@extends('layouts.admin')

@section('title', 'Asignar Usuarios al Rol')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Asignar Usuarios al Rol: {{ $role->display_name }}</h3>
                    <div>
                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver al Rol
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.roles.update-users', $role) }}">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Información:</strong> Selecciona los usuarios que tendrán asignado este rol.
                            Los usuarios seleccionados heredarán todos los permisos del rol "{{ $role->display_name }}".
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $role->getUsersCount() }}</h4>
                                        <p class="mb-0">Usuarios Actuales</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ count($role->permissions ?? []) }}</h4>
                                        <p class="mb-0">Permisos del Rol</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($users->count() > 0)
                            <div class="row">
                                <div class="col-12">
                                    <h5>Seleccionar Usuarios</h5>
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                        <label class="form-check-label" for="selectAll">
                                            <strong>Seleccionar/Deseleccionar todos</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                @foreach($users as $user)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card {{ $user->role_id == $role->_id ? 'border-primary' : '' }}">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input user-checkbox"
                                                       id="user_{{ $user->_id }}"
                                                       name="users[]"
                                                       value="{{ $user->_id }}"
                                                       {{ $user->role_id == $role->_id ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="user_{{ $user->_id }}">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                                 style="width: 40px; height: 40px;">
                                                                <i class="bi bi-person"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1" style="color: black;">{{ $user->name }} {{ $user->apellidos }}</h6>
                                                            <small class="text-muted">{{ $user->email }}</small>
                                                            <br>
                                                            <small class="text-muted">{{ ucfirst($user->rol) }}</small>
                                                            @if($user->role_id == $role->_id)
                                                                <span class="badge bg-success ms-2">Asignado</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                No hay usuarios disponibles para asignar a este rol.
                            </div>
                        @endif
                    </div>

                    @if($users->count() > 0)
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Asignaciones
                        </button>
                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');

    // Manejar seleccionar todos
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        // Actualizar estado del checkbox "seleccionar todos" cuando cambian los individuales
        userCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checkedCount = Array.from(userCheckboxes).filter(cb => cb.checked).length;

                if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === userCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            });
        });

        // Inicializar estado del checkbox "seleccionar todos"
        const initialCheckedCount = Array.from(userCheckboxes).filter(cb => cb.checked).length;
        if (initialCheckedCount === userCheckboxes.length && userCheckboxes.length > 0) {
            selectAllCheckbox.checked = true;
        } else if (initialCheckedCount > 0) {
            selectAllCheckbox.indeterminate = true;
        }
    }
});
</script>
@endsection