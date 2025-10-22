@extends('layouts.app')

@section('title', 'Solicitudes de Transferencia')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">
        <i class="bi bi-inbox"></i> Solicitudes de Transferencia de Red
    </h1>

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

    <div class="card">
        <div class="card-body">
            @if($solicitudes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Referidos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $solicitud)
                            <tr>
                                <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $solicitud->cliente_nombre }}</strong><br>
                                    <small class="text-muted">{{ $solicitud->cliente_email }}</small><br>
                                    <small class="text-muted">Código: {{ $solicitud->cliente_codigo_referido }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6">{{ $solicitud->total_nodos }}</span>
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
                                    <a href="{{ route('vendedor.transferencia-red.show', $solicitud->_id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver Detalle
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $solicitudes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">No tienes solicitudes de transferencia</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
