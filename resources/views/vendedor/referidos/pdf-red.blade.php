<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red de Referidos - {{ $vendedor->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .page-header {
            background: linear-gradient(135deg, #722F37 0%, #8b3c44 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .page-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #722F37;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            color: #722F37;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .info-item {
            display: table-cell;
            width: 33.33%;
            padding: 5px;
        }

        .info-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 18px;
            font-weight: bold;
            color: #722F37;
        }

        .stats-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            background: #ffffff;
        }

        .stat-icon {
            font-size: 24px;
            color: #722F37;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 18px;
            color: #722F37;
            margin: 20px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #722F37;
        }

        .network-tree {
            margin-bottom: 20px;
        }

        .level-1-node {
            background: #ffffff;
            border: 2px solid #722F37;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .node-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .node-info {
            display: table-cell;
            width: 70%;
        }

        .node-stats {
            display: table-cell;
            width: 30%;
            text-align: right;
        }

        .node-name {
            font-size: 16px;
            font-weight: bold;
            color: #722F37;
        }

        .node-email {
            font-size: 11px;
            color: #666;
        }

        .node-badge {
            display: inline-block;
            background: #722F37;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            margin-right: 5px;
        }

        .node-metric {
            font-size: 11px;
            color: #333;
            margin-top: 3px;
        }

        .node-metric strong {
            color: #722F37;
        }

        .level-2-container {
            margin-top: 15px;
            padding-left: 20px;
            border-left: 3px solid #e9ecef;
        }

        .level-2-header {
            font-size: 12px;
            font-weight: bold;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .level-2-node {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
        }

        .level-2-node .node-name {
            font-size: 14px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .footer-logo {
            font-size: 16px;
            font-weight: bold;
            color: #722F37;
            margin-bottom: 5px;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="page-header">
        <h1>🌳 Árbol de Red de Referidos</h1>
        <p>{{ $vendedor->name }} {{ $vendedor->apellidos ?? '' }}</p>
        <p style="font-size: 12px;">Generado el {{ $fechaExportacion }}</p>
    </div>

    {{-- Información del Vendedor --}}
    <div class="info-box">
        <h3>📊 Información del Vendedor</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Email</div>
                <div style="font-size: 12px;">{{ $vendedor->email }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Código Referido</div>
                <div style="font-size: 14px; font-weight: bold;">{{ $vendedor->codigo_referido }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Fecha Registro</div>
                <div style="font-size: 12px;">{{ $vendedor->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Estadísticas Generales --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value">{{ $nivel1Count }}</div>
            <div class="stat-label">Nivel 1</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🔗</div>
            <div class="stat-value">{{ $nivel2Count }}</div>
            <div class="stat-label">Nivel 2</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🌐</div>
            <div class="stat-value">{{ $totalRed }}</div>
            <div class="stat-label">Total Red</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value">${{ number_format($totalVentas, 0, ',', '.') }}</div>
            <div class="stat-label">Ventas Totales</div>
        </div>
    </div>

    {{-- Árbol de Red --}}
    <h2 class="section-title">📋 Estructura Detallada de la Red</h2>

    @if(count($redCompleta) > 0)
        <div class="network-tree">
            @foreach($redCompleta as $index => $nodo)
            <div class="level-1-node">
                <div class="node-header">
                    <div class="node-info">
                        <div class="node-name">
                            {{ $nodo['referido']->name }} {{ $nodo['referido']->apellidos ?? '' }}
                        </div>
                        <div class="node-email">{{ $nodo['referido']->email }}</div>
                        <div style="margin-top: 5px;">
                            <span class="node-badge">Nivel 1</span>
                            @if($nodo['referido']->activo ?? true)
                                <span class="node-badge" style="background: #10b981;">✓ Activo</span>
                            @else
                                <span class="node-badge" style="background: #6c757d;">○ Inactivo</span>
                            @endif
                        </div>
                    </div>
                    <div class="node-stats">
                        <div class="node-metric">
                            <strong>Ventas:</strong> ${{ number_format($nodo['referido']->total_ventas ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="node-metric">
                            <strong>Pedidos:</strong> {{ $nodo['referido']->pedidos_vendedor_count ?? 0 }}
                        </div>
                        <div class="node-metric">
                            <strong>Referidos:</strong> {{ $nodo['referido']->referidos_count ?? 0 }}
                        </div>
                    </div>
                </div>

                {{-- Sub-referidos (Nivel 2) --}}
                @if($nodo['sub_referidos']->count() > 0)
                <div class="level-2-container">
                    <div class="level-2-header">
                        🔸 {{ $nodo['sub_referidos']->count() }} Sub-referido{{ $nodo['sub_referidos']->count() > 1 ? 's' : '' }} (Nivel 2)
                    </div>
                    @foreach($nodo['sub_referidos'] as $subRef)
                    <div class="level-2-node">
                        <div class="node-header">
                            <div class="node-info">
                                <div class="node-name">
                                    {{ $subRef->name }} {{ $subRef->apellidos ?? '' }}
                                </div>
                                <div class="node-email">{{ $subRef->email }}</div>
                            </div>
                            <div class="node-stats">
                                <div class="node-metric">
                                    <strong>Ventas:</strong> ${{ number_format($subRef->total_ventas ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="node-metric">
                                    <strong>Pedidos:</strong> {{ $subRef->pedidos_vendedor_count ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">
            <p style="font-size: 16px; color: #666;">
                No hay referidos en tu red actualmente.
            </p>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-logo">Arepa la Llanerita</div>
        <p>Sistema de Gestión de Red de Referidos</p>
        <p>Documento generado automáticamente el {{ $fechaExportacion }}</p>
        <p style="margin-top: 5px; font-size: 9px;">
            Este documento contiene información confidencial. Uso exclusivo del vendedor.
        </p>
    </div>
</body>
</html>
