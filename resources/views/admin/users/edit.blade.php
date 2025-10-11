@extends('layouts.admin')

@section('title', '- Editar Usuario')
@section('page-title', 'Editar Usuario')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/users-edit.css') }}?v={{ filemtime(public_path('css/admin/users-edit.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="user-edit-header fade-in-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="user-edit-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    Editar Usuario
                </h1>
                <p class="user-edit-subtitle">
                    Modificando la información de <strong>{{ $user->name }} {{ $user->apellidos }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="user-edit-back-btn">
                    <i class="bi bi-arrow-left"></i>
                    Volver a la lista
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" id="editUserForm" novalidate>
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Columna Principal --}}
            <div class="col-lg-8">
                {{-- Información Personal --}}
                <div class="user-edit-card fade-in-up">
                    <div class="user-edit-card-header">
                        <i class="bi bi-person"></i>
                        <h3 class="user-edit-card-title">Información Personal</h3>
                    </div>
                    <div class="user-edit-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Nombres <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       placeholder="Ingresa los nombres">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">
                                    Apellidos <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('apellidos') is-invalid @enderror"
                                       id="apellidos"
                                       name="apellidos"
                                       value="{{ old('apellidos', $user->apellidos) }}"
                                       required
                                       placeholder="Ingresa los apellidos">
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cedula" class="form-label">
                                    Cédula <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('cedula') is-invalid @enderror"
                                       id="cedula"
                                       name="cedula"
                                       value="{{ old('cedula', $user->cedula) }}"
                                       required
                                       placeholder="Número de cédula">
                                @error('cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Solo números, sin puntos ni guiones</div>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_nacimiento" class="form-label">
                                    Fecha de Nacimiento <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                       id="fecha_nacimiento"
                                       name="fecha_nacimiento"
                                       value="{{ old('fecha_nacimiento', $user->fecha_nacimiento?->format('Y-m-d')) }}"
                                       required
                                       max="{{ now()->subYears(18)->format('Y-m-d') }}">
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Debe ser mayor de 18 años</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Información de Contacto --}}
                <div class="user-edit-card fade-in-up animate-delay-1">
                    <div class="user-edit-card-header">
                        <i class="bi bi-envelope"></i>
                        <h3 class="user-edit-card-title">Información de Contacto</h3>
                    </div>
                    <div class="user-edit-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       required
                                       placeholder="correo@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label">
                                    Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('telefono') is-invalid @enderror"
                                       id="telefono"
                                       name="telefono"
                                       value="{{ old('telefono', $user->telefono) }}"
                                       required
                                       placeholder="+57 300 123 4567">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="ciudad" class="form-label">
                                    Ciudad <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('ciudad') is-invalid @enderror"
                                       id="ciudad"
                                       name="ciudad"
                                       value="{{ old('ciudad', $user->ciudad) }}"
                                       required
                                       placeholder="Ciudad de residencia">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="departamento" class="form-label">
                                    Departamento <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('departamento') is-invalid @enderror"
                                       id="departamento"
                                       name="departamento"
                                       value="{{ old('departamento', $user->departamento) }}"
                                       required
                                       placeholder="Departamento de residencia">
                                @error('departamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror"
                                          id="direccion"
                                          name="direccion"
                                          rows="3"
                                          placeholder="Dirección completa (opcional)">{{ old('direccion', $user->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Seguridad --}}
                <div class="user-edit-card fade-in-up animate-delay-2">
                    <div class="user-edit-card-header">
                        <i class="bi bi-shield-lock"></i>
                        <h3 class="user-edit-card-title">Cambiar Contraseña</h3>
                    </div>
                    <div class="user-edit-card-body">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i>
                            Deja estos campos vacíos si no deseas cambiar la contraseña
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Mínimo 8 caracteres">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Repetir contraseña">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                        <i class="bi bi-eye" id="togglePasswordConfirmationIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Lateral --}}
            <div class="col-lg-4">
                {{-- Configuración del Sistema --}}
                <div class="user-edit-card fade-in-up animate-delay-3">
                    <div class="user-edit-card-header">
                        <i class="bi bi-gear"></i>
                        <h3 class="user-edit-card-title">Configuración del Sistema</h3>
                    </div>
                    <div class="user-edit-card-body">
                        <div class="mb-3">
                            <label for="rol" class="form-label">
                                Rol <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('rol') is-invalid @enderror"
                                    id="rol"
                                    name="rol"
                                    required>
                                <option value="">Seleccionar rol</option>
                                <option value="administrador" {{ old('rol', $user->rol) == 'administrador' ? 'selected' : '' }}>
                                    Administrador
                                </option>
                                <option value="lider" {{ old('rol', $user->rol) == 'lider' ? 'selected' : '' }}>
                                    Líder
                                </option>
                                <option value="vendedor" {{ old('rol', $user->rol) == 'vendedor' ? 'selected' : '' }}>
                                    Vendedor
                                </option>
                                <option value="cliente" {{ old('rol', $user->rol) == 'cliente' ? 'selected' : '' }}>
                                    Cliente
                                </option>
                            </select>
                            @error('rol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="referido_por" class="form-label">Referido por</label>
                            <select class="form-select @error('referido_por') is-invalid @enderror"
                                    id="referido_por"
                                    name="referido_por">
                                <option value="">Sin referidor</option>
                                @foreach($posibles_referidores as $referidor)
                                    <option value="{{ $referidor->id }}"
                                            {{ old('referido_por', $user->referido_por) == $referidor->id ? 'selected' : '' }}>
                                        {{ $referidor->name }} {{ $referidor->apellidos }} ({{ ucfirst($referidor->rol) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('referido_por')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meta_mensual" class="form-label">Meta Mensual</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('meta_mensual') is-invalid @enderror"
                                       id="meta_mensual"
                                       name="meta_mensual"
                                       value="{{ old('meta_mensual', $user->meta_mensual) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            @error('meta_mensual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="activo"
                                   name="activo"
                                   value="1"
                                   {{ old('activo', $user->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Usuario activo en el sistema
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Estadísticas MLM --}}
                <div class="user-edit-card fade-in-up animate-delay-3">
                    <div class="user-edit-card-header">
                        <i class="bi bi-graph-up"></i>
                        <h3 class="user-edit-card-title">Estadísticas MLM</h3>
                    </div>
                    <div class="user-edit-card-body">
                        <div class="mb-3">
                            <label for="ventas_mes_actual" class="form-label">Ventas Mes Actual</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('ventas_mes_actual') is-invalid @enderror"
                                       id="ventas_mes_actual"
                                       name="ventas_mes_actual"
                                       value="{{ old('ventas_mes_actual', $user->ventas_mes_actual) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            @error('ventas_mes_actual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comisiones_ganadas" class="form-label">Comisiones Ganadas</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('comisiones_ganadas') is-invalid @enderror"
                                       id="comisiones_ganadas"
                                       name="comisiones_ganadas"
                                       value="{{ old('comisiones_ganadas', $user->comisiones_ganadas) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            @error('comisiones_ganadas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comisiones_disponibles" class="form-label">Comisiones Disponibles</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('comisiones_disponibles') is-invalid @enderror"
                                       id="comisiones_disponibles"
                                       name="comisiones_disponibles"
                                       value="{{ old('comisiones_disponibles', $user->comisiones_disponibles) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            @error('comisiones_disponibles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr style="margin: 1.5rem 0; border-color: var(--gray-200);">

                        <div class="user-edit-stats-item">
                            <span class="user-edit-stats-label">Total Referidos:</span>
                            <strong class="user-edit-stats-value">{{ $user->total_referidos ?? 0 }}</strong>
                        </div>

                        <div class="user-edit-stats-item">
                            <span class="user-edit-stats-label">Código Referido:</span>
                            <span class="user-edit-badge badge-info">{{ $user->codigo_referido }}</span>
                        </div>

                        <div class="user-edit-stats-item">
                            <span class="user-edit-stats-label">Registro:</span>
                            <strong class="user-edit-stats-value">{{ $user->created_at->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="user-edit-card fade-in-up animate-delay-3">
                    <div class="user-edit-card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i>
                                Guardar Cambios
                            </button>

                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye"></i>
                                Ver Detalles
                            </a>

                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" data-ignore-changes>
                                <i class="bi bi-x-circle"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/users-edit.js') }}?v={{ filemtime(public_path('js/admin/users-edit.js')) }}"></script>
@endpush
