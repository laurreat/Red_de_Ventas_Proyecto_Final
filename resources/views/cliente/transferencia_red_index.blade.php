@extends('layouts.app')

@section('title', 'Transferir Mi Red')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="bi bi-diagram-3"></i> Transferir Mi Red de Referidos
            </h1>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Información de la Red --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Tu Red Actual</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h2 class="text-primary mb-2">{{ $totalNodos }}</h2>
                                <p class="text-muted mb-0">Total de Referidos</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h2 class="text-success mb-2">{{ $user->codigo_referido }}</h2>
                                <p class="text-muted mb-0">Tu Código de Referido</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h2 class="text-info mb-2">Cliente</h2>
                                <p class="text-muted mb-0">Tu Rol Actual</p>
                            </div>
                        </div>
                    </div>

                    @if($totalNodos > 0)
                        <div class="alert alert-info mt-4">
                            <i class="bi bi-lightbulb"></i>
                            <strong>¿Quieres crecer tu red?</strong> 
                            Puedes transferir tu red completa de {{ $totalNodos }} referidos a un vendedor o líder para que te ayude a hacer crecer tu negocio.
                        </div>
                    @else
                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-exclamation-circle"></i>
                            No tienes referidos aún. Primero invita a tus amigos usando tu código de referido.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Formulario de Solicitud --}}
            @if($totalNodos > 0)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-send"></i> Solicitar Transferencia</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cliente.transferencia-red.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="codigo_vendedor" class="form-label">
                                <strong>Código del Vendedor/Líder</strong>
                            </label>
                            <input type="text" 
                                   class="form-control @error('codigo_vendedor') is-invalid @enderror" 
                                   id="codigo_vendedor" 
                                   name="codigo_vendedor" 
                                   placeholder="Ejemplo: ABC12345"
                                   required>
                            @error('codigo_vendedor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Ingresa el código de referido del vendedor o líder al que deseas transferir tu red.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje (opcional)</label>
                            <textarea class="form-control @error('mensaje') is-invalid @enderror" 
                                      id="mensaje" 
                                      name="mensaje" 
                                      rows="3" 
                                      placeholder="Cuéntale al vendedor/líder por qué quieres unirte a su red..."
                                      maxlength="500"></textarea>
                            @error('mensaje')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Importante:</strong> Al transferir tu red, todos tus {{ $totalNodos }} referidos pasarán a estar bajo la gestión del vendedor/líder seleccionado. Esta acción no se puede deshacer.
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-send"></i> Enviar Solicitud
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Historial de Solicitudes --}}
            @if($solicitudes->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Solicitudes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Vendedor/Líder</th>
                                    <th>Nodos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        {{ $solicitud->vendedor_nombre }}<br>
                                        <small class="text-muted">{{ $solicitud->vendedor_email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $solicitud->total_nodos }} referidos</span>
                                    </td>
                                    <td>
                                        @if($solicitud->estado === 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($solicitud->estado === 'aprobada')
                                            <span class="badge bg-success">Aprobada</span>
                                        @elseif($solicitud->estado === 'rechazada')
                                            <span class="badge bg-danger">Rechazada</span>
                                        @else
                                            <span class="badge bg-secondary">Cancelada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($solicitud->estado === 'pendiente')
                                            <form action="{{ route('cliente.transferencia-red.cancelar', $solicitud->_id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Seguro que deseas cancelar esta solicitud?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                            </form>
                                        @elseif($solicitud->estado === 'rechazada')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#motivoModal{{ $solicitud->_id }}">
                                                <i class="bi bi-eye"></i> Ver Motivo
                                            </button>

                                            {{-- Modal Motivo Rechazo --}}
                                            <div class="modal fade" id="motivoModal{{ $solicitud->_id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Motivo de Rechazo</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>{{ $solicitud->motivo_rechazo }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
