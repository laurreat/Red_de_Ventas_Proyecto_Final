@extends('layouts.vendedor')

@section('title', ' - Árbol de Red de Referidos')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="page-header-modern fade-in">
        <div class="page-header-content">
            <div class="page-header-left">
                <div class="page-icon-wrapper">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <h1 class="page-title">Mi Red de Ventas</h1>
                    <p class="page-subtitle">
                        <i class="bi bi-layers-fill me-2"></i>
                        Estructura Multinivel - 2 Niveles de Profundidad
                    </p>
                </div>
            </div>
            <div class="page-header-actions">
                <a href="{{ route('vendedor.referidos.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver</span>
                </a>
                <button class="btn btn-primary" id="export-tree-btn">
                    <i class="bi bi-download"></i>
                    <span>Exportar</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="network-quick-stats fade-in-up animate-delay-1">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="quick-stat-card">
                    <div class="quick-stat-icon" style="background: linear-gradient(135deg, #722F37, #8b3c44);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="quick-stat-info">
                        <div class="quick-stat-value">{{ count($redCompleta) }}</div>
                        <div class="quick-stat-label">Nivel 1</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="quick-stat-card">
                    <div class="quick-stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <div class="quick-stat-info">
                        <div class="quick-stat-value">{{ collect($redCompleta)->sum(function($nodo) { return $nodo['sub_referidos']->count(); }) }}</div>
                        <div class="quick-stat-label">Nivel 2</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="quick-stat-card">
                    <div class="quick-stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="quick-stat-info">
                        <div class="quick-stat-value">{{ count($redCompleta) + collect($redCompleta)->sum(function($nodo) { return $nodo['sub_referidos']->count(); }) }}</div>
                        <div class="quick-stat-label">Total Red</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="quick-stat-card">
                    <div class="quick-stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="quick-stat-info">
                        <div class="quick-stat-value">${{ number_format(collect($redCompleta)->sum(function($nodo) { return $nodo['referido']->total_ventas ?? 0; }) / 1000, 0) }}k</div>
                        <div class="quick-stat-label">Ventas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Árbol de Red --}}
    <div class="network-tree-section fade-in-up animate-delay-2">
        <div class="section-header">
            <h2 class="section-title">
                <i class="bi bi-diagram-2-fill"></i>
                Estructura de tu Red
            </h2>
            <div class="section-badge">{{ count($redCompleta) }} {{ count($redCompleta) == 1 ? 'Referido Directo' : 'Referidos Directos' }}</div>
        </div>

        @if(count($redCompleta) > 0)
        {{-- Usuario Principal (Tú) --}}
        <div class="network-root-node">
            <div class="root-node-card">
                <div class="root-node-crown">
                    <i class="bi bi-award-fill"></i>
                </div>
                <div class="root-node-avatar">
                    <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellidos ?? '', 0, 1)) }}</span>
                </div>
                <div class="root-node-info">
                    <h3 class="root-node-name">{{ auth()->user()->name }} {{ auth()->user()->apellidos }}</h3>
                    <p class="root-node-role">
                        <i class="bi bi-person-badge-fill me-1"></i>
                        Centro de la Red
                    </p>
                    <div class="root-node-code">
                        <span class="code-label">Código:</span>
                        <span class="code-value">{{ auth()->user()->codigo_referido }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Conexiones Visuales --}}
        <div class="network-connector"></div>

        {{-- Red de Nivel 1 --}}
        <div class="network-level-1">
            <div class="row g-4">

            {{-- Referidos Nivel 1 --}}
            @foreach($redCompleta as $index => $nodo)
            <div class="col-lg-6 mb-4" style="animation-delay: {{ ($index * 0.1) + 0.3 }}s;">
                <div class="member-card level-1-card">
                    <div class="member-card-header">
                        <div class="member-header-left">
                            <div class="member-avatar">
                                <span>{{ strtoupper(substr($nodo['referido']->name, 0, 1)) }}{{ strtoupper(substr($nodo['referido']->apellidos ?? '', 0, 1)) }}</span>
                            </div>
                            <div class="member-info">
                                <h4 class="member-name">{{ $nodo['referido']->name }} {{ $nodo['referido']->apellidos }}</h4>
                                <p class="member-email">
                                    <i class="bi bi-envelope-fill me-1"></i>
                                    {{ $nodo['referido']->email }}
                                </p>
                            </div>
                        </div>
                        <div class="member-badge level-1-badge">
                            <i class="bi bi-star-fill"></i>
                            <span>Nivel 1</span>
                        </div>
                    </div>

                    <div class="member-card-stats">
                        <div class="stat-item">
                            <div class="stat-icon" style="background: rgba(114, 47, 55, 0.1); color: #722F37;">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Registro</div>
                                <div class="stat-value">{{ $nodo['referido']->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Ventas</div>
                                <div class="stat-value">${{ number_format($nodo['referido']->total_ventas ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                <i class="bi bi-bag-check-fill"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Pedidos</div>
                                <div class="stat-value">{{ $nodo['referido']->pedidos_vendedor_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Referidos (Nivel 2) --}}
                    @if($nodo['sub_referidos']->count() > 0)
                    <div class="member-subreferidos">
                        <div class="subreferidos-header">
                            <h5 class="subreferidos-title">
                                <i class="bi bi-people-fill"></i>
                                Sus Referidos ({{ $nodo['sub_referidos']->count() }})
                            </h5>
                        </div>
                        <div class="subreferidos-grid">
                            @foreach($nodo['sub_referidos'] as $subReferido)
                            <div class="subreferido-item">
                                <div class="subreferido-avatar">
                                    <span>{{ strtoupper(substr($subReferido->name, 0, 1)) }}{{ strtoupper(substr($subReferido->apellidos ?? '', 0, 1)) }}</span>
                                </div>
                                <div class="subreferido-info">
                                    <div class="subreferido-name">{{ $subReferido->name }}</div>
                                    <div class="subreferido-stats">
                                        <span class="subreferido-stat">
                                            <i class="bi bi-bag-fill"></i>
                                            {{ $subReferido->pedidos_vendedor_count ?? 0 }}
                                        </span>
                                        <span class="subreferido-stat">
                                            <i class="bi bi-cash"></i>
                                            ${{ number_format($subReferido->total_ventas ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="member-subreferidos-empty">
                        <i class="bi bi-inbox"></i>
                        <p>Sin referidos de nivel 2</p>
                    </div>
                    @endif

                    <div class="member-card-footer">
                        <a href="{{ route('vendedor.referidos.show', $nodo['referido']->id) }}" class="member-action-btn">
                            <i class="bi bi-eye-fill"></i>
                            <span>Ver Detalles Completos</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
        </div>
        @else
        {{-- Estado Vacío Mejorado --}}
        <div class="network-empty-state">
            <div class="empty-state-illustration">
                <i class="bi bi-diagram-3"></i>
            </div>
            <h3 class="empty-state-title">Tu Red Está en Construcción</h3>
            <p class="empty-state-text">
                Comienza a construir tu imperio de ventas invitando a personas a unirse a tu equipo.
                Cada referido es una oportunidad para crecer juntos.
            </p>
            <button class="empty-state-btn" id="generate-link-btn">
                <i class="bi bi-share-fill"></i>
                Compartir mi Enlace de Referido
            </button>
        </div>
        @endif
    </div>
</div>

<input type="hidden" id="codigo-referido" value="{{ auth()->user()->codigo_referido }}">

<div id="loading-overlay" class="referidos-loading-overlay">
    <div>
        <div class="referidos-loading-spinner"></div>
        <div class="referidos-loading-text">Cargando...</div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/referidos-modern.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/vendedor/red-referidos-enhanced.css') }}?v={{ time() }}">
<style>
/* Network Tree Enhanced Styles */
.network-header {
    background: linear-gradient(135deg, #722F37 0%, #5a252a 100%);
    border-radius: 20px;
    padding: 0;
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    box-shadow: 0 20px 40px rgba(114, 47, 55, 0.2);
}

.network-header-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.5;
}

.network-header-content {
    position: relative;
    z-index: 2;
    padding: 2rem;
}

.network-header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.network-header-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.network-header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.network-header-title {
    font-size: 2rem;
    font-weight: 800;
    color: white;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.5px;
}

.network-header-subtitle {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    display: flex;
    align-items: center;
}

.network-header-actions {
    display: flex;
    gap: 1rem;
}

.network-btn {
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
}

.network-btn-primary {
    background: white;
    color: #722F37;
}

.network-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 255, 255, 0.3);
    color: #722F37;
}

.network-btn-outline {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.network-btn-outline:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
}

.network-quick-stats {
    margin-bottom: 2.5rem;
}

.quick-stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.quick-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.quick-stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
    flex-shrink: 0;
}

.quick-stat-info {
    flex: 1;
}

.quick-stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #111827;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.quick-stat-label {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.network-tree-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    color: #722F37;
}

.section-badge {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #722F37, #8b3c44);
    color: white;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.network-root-node {
    margin-bottom: 3rem;
}

.root-node-card {
    max-width: 500px;
    margin: 0 auto;
    background: linear-gradient(135deg, #722F37 0%, #5a252a 100%);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    position: relative;
    box-shadow: 0 10px 40px rgba(114, 47, 55, 0.3);
}

.root-node-crown {
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 50px;
    background: #f59e0b;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    border: 4px solid white;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
}

.root-node-avatar {
    width: 90px;
    height: 90px;
    margin: 0 auto 1rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 800;
    color: white;
    border: 4px solid rgba(255, 255, 255, 0.3);
}

.root-node-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin: 0 0 0.5rem 0;
}

.root-node-role {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    margin: 0 0 1rem 0;
}

.root-node-code {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    border: 2px dashed rgba(255, 255, 255, 0.3);
}

.code-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    margin-right: 0.5rem;
}

.code-value {
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    font-family: 'Courier New', monospace;
    letter-spacing: 2px;
}

.network-connector {
    height: 40px;
    background: linear-gradient(180deg, #722F37 0%, transparent 100%);
    width: 2px;
    margin: 0 auto 2rem;
    opacity: 0.3;
}

.network-level-1 {
    position: relative;
}

.member-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

.member-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    border-color: #722F37;
}

.level-1-card {
    border-top: 4px solid #722F37;
}

.member-card-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.member-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
    min-width: 0;
}

.member-avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #722F37, #8b3c44);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 800;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(114, 47, 55, 0.2);
}

.member-info {
    flex: 1;
    min-width: 0;
}

.member-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.25rem 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.member-email {
    font-size: 0.85rem;
    color: #6b7280;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.member-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    flex-shrink: 0;
}

.level-1-badge {
    background: linear-gradient(135deg, #722F37, #8b3c44);
    color: white;
}

.member-card-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.stat-value {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.member-subreferidos {
    padding: 1.5rem;
    background: #f9fafb;
    border-top: 2px dashed #e5e7eb;
}

.subreferidos-header {
    margin-bottom: 1rem;
}

.subreferidos-title {
    font-size: 1rem;
    font-weight: 700;
    color: #3b82f6;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.subreferidos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.subreferido-item {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border: 2px solid #e5e7eb;
    transition: all 0.2s ease;
}

.subreferido-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
}

.subreferido-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
}

.subreferido-info {
    flex: 1;
    min-width: 0;
}

.subreferido-name {
    font-weight: 600;
    color: #111827;
    font-size: 0.9rem;
    margin-bottom: 0.35rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.subreferido-stats {
    display: flex;
    gap: 0.75rem;
}

.subreferido-stat {
    font-size: 0.75rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.member-subreferidos-empty {
    padding: 1.5rem;
    background: #f9fafb;
    border-top: 2px dashed #e5e7eb;
    text-align: center;
    color: #9ca3af;
}

.member-subreferidos-empty i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}

.member-subreferidos-empty p {
    margin: 0;
    font-size: 0.9rem;
}

.member-card-footer {
    padding: 1.5rem;
    border-top: 2px solid #f3f4f6;
}

.member-action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.875rem;
    background: linear-gradient(135deg, #722F37, #8b3c44);
    color: white;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.member-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(114, 47, 55, 0.3);
    color: white;
}

.network-empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state-illustration {
    font-size: 6rem;
    color: #e5e7eb;
    margin-bottom: 1.5rem;
}

.empty-state-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 1rem;
}

.empty-state-text {
    font-size: 1.1rem;
    color: #6b7280;
    max-width: 600px;
    margin: 0 auto 2rem;
    line-height: 1.6;
}

.empty-state-btn {
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #722F37, #8b3c44);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.empty-state-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(114, 47, 55, 0.3);
}

@media (max-width: 768px) {
    .network-header-top {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .network-header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .network-btn {
        width: 100%;
        justify-content: center;
    }
    
    .network-header-title {
        font-size: 1.5rem;
    }
    
    .member-card-stats {
        grid-template-columns: 1fr;
    }
    
    .subreferidos-grid {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportTreeBtn = document.getElementById('export-tree-btn');
    if (exportTreeBtn) {
        exportTreeBtn.addEventListener('click', function() {
            // Mostrar notificación
            if (window.Livewire) {
                Livewire.dispatch('show-toast', { 
                    type: 'info', 
                    message: 'Generando PDF del árbol de red...'
                });
            }
            
            // Redirigir a la ruta de exportación
            setTimeout(() => {
                window.location.href = '{{ route('vendedor.referidos.red.exportar') }}';
                
                // Mostrar notificación de éxito después de un tiempo
                setTimeout(() => {
                    if (window.Livewire) {
                        Livewire.dispatch('show-toast', { 
                            type: 'success', 
                            message: 'Exportación Completa: El árbol de tu red se ha exportado correctamente en PDF'
                        });
                    }
                }, 2000);
            }, 500);
        });
    }
});
</script>
@endpush
