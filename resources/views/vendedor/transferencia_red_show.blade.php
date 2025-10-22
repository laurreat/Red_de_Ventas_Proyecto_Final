@extends('layouts.app')

@section('title', 'Detalle de Solicitud')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-file-text"></i> Detalle de Solicitud</h1>
        <a href="{{ route('vendedor.transferencia-red.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Información de la Solicitud --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nombre:</strong> {{ $solicitud->cliente_nombre }}</p>
                    <p><strong>Email:</strong> {{ $solicitud->cliente_email }}</p>
                    <p><strong>Código Referido:</strong> {{ $solicitud->cliente_codigo_referido }}</p>
                    <p><strong>Fecha Solicitud:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Información de la Red</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="text-primary">{{ $solicitud->total_nodos }}</h2>
                        <p class="text-muted">Total de Referidos</p>
                    </div>
                    <p><strong>Estado:</strong> 
                        @if($solicitud->estado === 'pendiente')
                            <span class="badge bg-warning">Pendiente</span>
                        @elseif($solicitud->estado === 'aprobada')
                            <span class="badge bg-success">Aprobada</span>
                        @else
                            <span class="badge bg-danger">Rechazada</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensaje del Cliente --}}
    @if($solicitud->mensaje_cliente)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-chat-quote"></i> Mensaje del Cliente</h5>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $solicitud->mensaje_cliente }}</p>
        </div>
    </div>
    @endif

    {{-- Listado de Nodos --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-people"></i> Referidos a Transferir ({{ $nodos->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nodos as $nodo)
                        <tr>
                            <td>{{ $nodo->nombreCompleto() }}</td>
                            <td>{{ $nodo->email }}</td>
                            <td>{{ $nodo->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    @if($solicitud->estado === 'pendiente')
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="bi bi-check-circle"></i> Aprobar Solicitud
                    </h5>
                    <p class="card-text">Acepta la transferencia de {{ $solicitud->total_nodos }} referidos a tu red.</p>
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#aprobarModal">
                        <i class="bi bi-check-lg"></i> Aprobar Transferencia
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="bi bi-x-circle"></i> Rechazar Solicitud
                    </h5>
                    <p class="card-text">Rechaza la solicitud y notifica al cliente el motivo.</p>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rechazarModal">
                        <i class="bi bi-x-lg"></i> Rechazar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Aprobar --}}
    <div class="modal fade" id="aprobarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('vendedor.transferencia-red.aprobar', $solicitud->_id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Confirmar Aprobación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas aprobar esta solicitud?</p>
                        <p><strong>Se transferirán {{ $solicitud->total_nodos }} referidos a tu red.</strong></p>
                        
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje para el cliente (opcional)</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Aprobar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Rechazar --}}
    <div class="modal fade" id="rechazarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('vendedor.transferencia-red.rechazar', $solicitud->_id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Rechazar Solicitud</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Por favor, indica el motivo del rechazo:</p>
                        
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo de rechazo *</label>
                            <textarea class="form-control" id="motivo" name="motivo" rows="3" required maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-lg"></i> Rechazar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
