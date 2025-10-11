{{-- VERSIÓN SIMPLIFICADA PARA TESTING --}}

{{-- Mensajes de éxito --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <strong>¡Éxito!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Mensajes de error --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>¡Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Mensajes de advertencia --}}
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show">
        <strong>¡Advertencia!</strong> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Mensajes informativos --}}
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        <strong>Información:</strong> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Errores de validación --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>¡Hay problemas en el formulario!</strong>
        <ul class="mt-2 mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif