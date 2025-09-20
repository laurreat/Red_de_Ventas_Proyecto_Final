@foreach($red as $nodo)
<div class="red-node position-relative" style="margin-left: {{ ($nivel - 1) * 25 }}px;">
    <div class="d-flex align-items-center py-2 border rounded mb-2"
         style="background-color: {{ $nivel % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
        <div class="me-3">
            <div class="bg-{{ $nodo['usuario']->rol == 'lider' ? 'warning' : 'info' }} rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 35px; height: 35px;">
                <i class="bi bi-person text-white"></i>
            </div>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-medium" style="color: black;">{{ $nodo['usuario']->name }}</div>
                    <small class="text-muted" style="color: black;">{{ $nodo['usuario']->email }}</small>
                    <div class="mt-1">
                        <span class="nivel-badge badge bg-secondary">Nivel {{ $nodo['nivel'] + 1 }}</span>
                        <span class="badge bg-{{ $nodo['usuario']->rol == 'lider' ? 'warning' : 'info' }} ms-1">
                            {{ ucfirst($nodo['usuario']->rol) }}
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block" style="color: black;">
                        {{ $nodo['usuario']->created_at->format('d/m/Y') }}
                    </small>
                    @if($nodo['usuario']->codigo_referido)
                        <small class="text-muted" style="color: black;">
                            Código: {{ $nodo['usuario']->codigo_referido }}
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(count($nodo['hijos']) > 0 && $nivel < 5)
        @include('admin.referidos.partials.red-node', ['red' => $nodo['hijos'], 'nivel' => $nivel + 1])
    @elseif($nivel >= 5 && count($nodo['hijos']) > 0)
        <div class="text-center py-2" style="margin-left: 25px;">
            <small class="text-muted" style="color: black;">
                <i class="bi bi-three-dots"></i>
                {{ count($nodo['hijos']) }} referidos más (máximo 5 niveles mostrados)
            </small>
        </div>
    @endif
</div>
@endforeach