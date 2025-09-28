<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Comisiones</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
            @top-center {
                content: "Reporte de Comisiones - Arepa la Llanerita";
            }
            @bottom-center {
                content: "Página " counter(page) " de " counter(pages);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #722F37;
        }

        .logo {
            color: #722F37;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .period {
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
            font-size: 12px;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 25px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #722F37 0%, #8B3A42 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(114, 47, 55, 0.2);
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 10px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #722F37;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #722F37;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .table th {
            background: linear-gradient(135deg, #722F37 0%, #8B3A42 100%);
            color: white;
            font-weight: bold;
            padding: 12px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 10px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #fff3cd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-success {
            color: #28a745;
            font-weight: bold;
        }

        .text-primary {
            color: #007bff;
            font-weight: bold;
        }

        .text-warning {
            color: #ffc107;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            min-width: 35px;
        }

        .badge-primary {
            background-color: #007bff;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .summary-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #dee2e6;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 12px;
            color: #722F37;
        }

        .footer {
            position: fixed;
            bottom: 1cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .no-data-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ccc;
        }

        .page-break {
            page-break-before: always;
        }

        .rank {
            display: inline-block;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            text-align: center;
            line-height: 25px;
            font-weight: bold;
            font-size: 10px;
            margin-right: 5px;
        }

        .rank-1 { background: #FFD700; color: #333; }
        .rank-2 { background: #C0C0C0; color: #333; }
        .rank-3 { background: #CD7F32; color: white; }
        .rank-other { background: #e9ecef; color: #495057; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🫓 AREPA LA LLANERITA</div>
        <div class="subtitle">Reporte Detallado de Comisiones de Vendedores</div>
        <div class="period">
            Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">${{ number_format($stats['total_comisiones'], 0) }}</div>
            <div class="stat-label">Total Comisiones</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">${{ number_format($stats['total_ventas'], 0) }}</div>
            <div class="stat-label">Total Ventas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['vendedores_activos'] }}</div>
            <div class="stat-label">Vendedores Activos</div>
        </div>
    </div>

    <!-- Tabla de Comisiones -->
    <div class="section-title">📊 Detalle de Comisiones por Vendedor</div>

    @if($comisiones->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 25%">Vendedor</th>
                    <th style="width: 20%">Contacto</th>
                    <th style="width: 10%" class="text-center">Pedidos</th>
                    <th style="width: 10%" class="text-center">Entregados</th>
                    <th style="width: 15%" class="text-right">Total Ventas</th>
                    <th style="width: 15%" class="text-right">Comisión</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comisiones as $index => $comision)
                <tr>
                    <td class="text-center">
                        @if($index < 3)
                            <span class="rank rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                        @else
                            <span class="rank rank-other">{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td class="font-bold">{{ $comision['Vendedor'] }}</td>
                    <td>
                        <div>{{ $comision['Email'] }}</div>
                        <div style="font-size: 9px; color: #666;">{{ $comision['Teléfono'] }}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-primary">{{ $comision['Total Pedidos'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-success">{{ $comision['Pedidos Entregados'] }}</span>
                    </td>
                    <td class="text-right font-bold">${{ number_format($comision['Total Ventas'], 0) }}</td>
                    <td class="text-right text-success">${{ number_format($comision['Comisión Ganada'], 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Resumen Estadístico -->
        <div class="summary-section">
            <div class="section-title" style="margin-top: 0;">📈 Resumen Estadístico</div>
            <div class="summary-grid">
                <div>
                    <div class="summary-item">
                        <span>Total de Pedidos:</span>
                        <span class="font-bold">{{ number_format($stats['total_pedidos']) }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Pedidos Entregados:</span>
                        <span class="font-bold text-success">{{ number_format($stats['total_entregados']) }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Tasa de Entrega:</span>
                        <span class="font-bold">{{ $stats['total_pedidos'] > 0 ? number_format(($stats['total_entregados'] / $stats['total_pedidos']) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
                <div>
                    <div class="summary-item">
                        <span>Promedio Comisión:</span>
                        <span class="font-bold">${{ number_format($stats['promedio_comision'], 0) }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Porcentaje Comisión:</span>
                        <span class="font-bold">10%</span>
                    </div>
                    <div class="summary-item">
                        <span>TOTAL COMISIONES:</span>
                        <span class="font-bold text-success">${{ number_format($stats['total_comisiones'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <div class="no-data-icon">📊</div>
            <h3>No hay datos disponibles</h3>
            <p>No se encontraron comisiones en el período seleccionado.</p>
        </div>
    @endif

    <div class="footer">
        <div>Arepa la Llanerita - Sistema de Gestión de Ventas</div>
        <div>Generado el {{ now()->format('d/m/Y H:i:s') }} | Documento confidencial</div>
    </div>
</body>
</html>