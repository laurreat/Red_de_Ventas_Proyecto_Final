@extends('layouts.admin')

@section('title', 'Crear Rol')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Crear Nuevo Rol</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre del Rol <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required>
                                    <small class="form-text text-muted">Nombre único del rol (sin espacios, minúsculas)</small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_name">Nombre para Mostrar <span class="text-danger">*</span></label>
                                    <input type="text" name="display_name" id="display_name" class="form-control @error('display_name') is-invalid @enderror"
                                           value="{{ old('display_name') }}" required>
                                    <small class="form-text text-muted">Nombre que se mostrará en la interfaz</small>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Descripción del rol y sus responsabilidades</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="active" name="active" value="1"
                                       {{ old('active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="active">Rol Activo</label>
                            </div>
                        </div>

                        <hr>

                        <h5>Permisos del Rol</h5>
                        <p class="text-muted">Selecciona los permisos que tendrá este rol:</p>

                        <div class="row">
                            @foreach($categories as $categoryKey => $categoryName)
                                @php
                                    $categoryPermissions = array_filter($permissions, function($permission) use ($categoryKey) {
                                        return $permission['category'] === $categoryKey;
                                    });
                                @endphp
                                @if(!empty($categoryPermissions))
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <input type="checkbox" class="category-toggle me-2" data-category="{{ $categoryKey }}">
                                                {{ $categoryName }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($categoryPermissions as $permission)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input permission-checkbox"
                                                       data-category="{{ $categoryKey }}"
                                                       id="permission_{{ $permission['name'] }}"
                                                       name="permissions[]"
                                                       value="{{ $permission['name'] }}"
                                                       {{ in_array($permission['name'], old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission['name'] }}">
                                                    <strong>{{ $permission['display_name'] }}</strong>
                                                    @if($permission['description'])
                                                        <br><small class="text-muted">{{ $permission['description'] }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Rol
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/roles-forms.js') }}"></script>
@endpush