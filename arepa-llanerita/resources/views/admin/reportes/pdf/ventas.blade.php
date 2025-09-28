<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas - Arepa la Llanerita</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header h2 {
            font-size: 16px;
            font-weight: normal;
            opacity: 0.9;
        }

        .header .period {
            font-size: 14px;
            margin-top: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        .info-bar {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #722f37;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }

        .info-bar .generated {
            font-size: 11px;
            color: #666;
            float: right;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #722f37;
            margin-bottom: 5px;
        }

        .stat-card .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #722f37;
            color: white;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-weight: bold;
            border-radius: 5px;
            font-size: 14px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
        }

        .table th {
            background: #f8f9fa;
            color: #495057;
            font-weight: bold;
            padding: 12px 8px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: top;
        }

        .table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .table tr:hover {
            background: #e9ecef;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .currency {
            color: #28a745;
            font-weight: bold;
        }

        .number {
            color: #007bff;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            background: white;
        }

        .page-break {
            page-break-before: always;
        }

        .small-table th,
        .small-table td {
            padding: 8px 6px;
            font-size: 11px;
        }

        .summary-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-box h4 {
            color: #722f37;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .chart-placeholder {
            height: 150px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🥟 AREPA LA LLANERITA</h1>
        <h2>Reporte de Ventas</h2>
        <div class="period">Período: {{ $fechaInicio }} al {{ $fechaFin }}</div>
    </div>

    <!-- Info Bar -->
    <div class="info-bar">
        <strong>Sistema de Gestión de Ventas</strong>
        <div class="generated">Generado el {{ now()->format('d/m/Y H:i:s') }}</div>
        <div style="clear: both;"></div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="value">{{ number_format(to_float($stats['total_ventas'])) }}</div>
            <div class="label">Total de Ventas</div>
        </div>
        <div class="stat-card">
            <div class="value">${{ number_format(to_float($stats['total_ingresos']), 0, ',', '.') }}</div>
            <div class="label">Total de Ingresos</div>
        </div>
        <div class="stat-card">
            <div class="value">${{ number_format(to_float($stats['ticket_promedio']), 0, ',', '.') }}</div>
            <div class="label">Ticket Promedio</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ number_format(to_float($stats['productos_vendidos'])) }}</div>
            <div class="label">Productos Vendidos</div>
        </div>
    </div>

    <!-- Ventas por Día -->
    <div class="section">
        <div class="section-title">📅 Ventas por Día</div>
        @if($ventasPorDia && $ventasPorDia->count() > 0)
            <table class="table small-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th class="text-center">Cantidad de Pedidos</th>
                        <th class="text-right">Total Vendido</th>
                        <th class="text-right">Promedio por Pedido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventasPorDia as $fecha => $datos)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                        <td class="text-center number">{{ $datos['cantidad'] }}</td>
                        <td class="text-right currency">${{ number_format(to_float($datos['total']), 0, ',', '.') }}</td>
                        <td class="text-right">${{ number_format($datos['cantidad'] > 0 ? to_float($datos['total']) / to_float($datos['cantidad']) : 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No hay datos de ventas para el período seleccionado</div>
        @endif
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Ventas por Vendedor -->
    <div class="section">
        <div class="section-title">👥 Ventas por Vendedor</div>
        @if($ventasPorVendedor && $ventasPorVendedor->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Vendedor</th>
                        <th>Email</th>
                        <th class="text-center">Pedidos</th>
                        <th class="text-right">Total Ventas</th>
                        <th class="text-right">Comisión Estimada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventasPorVendedor as $vendedor)
                    <tr>
                        <td class="font-weight-bold">{{ $vendedor['vendedor'] }}</td>
                        <td class="text-muted">{{ $vendedor['email'] }}</td>
                        <td class="text-center number">{{ $vendedor['cantidad_pedidos'] }}</td>
                        <td class="text-right currency">${{ number_format(to_float($vendedor['total_ventas']), 0, ',', '.') }}</td>
                        <td class="text-right currency">${{ number_format(to_float($vendedor['comision_estimada']), 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No hay datos de vendedores para este período</div>
        @endif
    </div>

    <!-- Productos Más Vendidos -->
    <div class="section">
        <div class="section-title">🏆 Top 10 Productos Más Vendidos</div>
        @if($productosMasVendidos && $productosMasVendidos->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th class="text-center">Cantidad Vendida</th>
                        <th class="text-right">Ingresos Totales</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productosMasVendidos as $producto)
                    <tr>
                        <td class="text-center">
                            <strong style="color: #722f37;">{{ $loop->iteration }}°</strong>
                        </td>
                        <td class="font-weight-bold">{{ $producto['producto'] }}</td>
                        <td class="text-muted">{{ $producto['categoria'] }}</td>
                        <td class="text-center number">{{ $producto['cantidad_vendida'] }}</td>
                        <td class="text-right currency">${{ number_format(to_float($producto['total_ingresos']), 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No hay datos de productos para este período</div>
        @endif
    </div>

    <!-- Clientes Más Activos -->
    <div class="section">
        <div class="section-title">⭐ Top 10 Clientes Más Activos</div>
        @if($clientesMasActivos && $clientesMasActivos->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th class="text-center">Pedidos</th>
                        <th class="text-right">Total Gastado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientesMasActivos as $cliente)
                    <tr>
                        <td class="text-center">
                            <strong style="color: #722f37;">{{ $loop->iteration }}°</strong>
                        </td>
                        <td class="font-weight-bold">{{ $cliente['cliente'] }}</td>
                        <td class="text-muted">{{ $cliente['email'] }}</td>
                        <td class="text-center number">{{ $cliente['cantidad_pedidos'] }}</td>
                        <td class="text-right currency">${{ number_format(to_float($cliente['total_gastado']), 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No hay datos de clientes para este período</div>
        @endif
    </div>

    <!-- Resumen por Estado -->
    @if($ventasPorEstado && $ventasPorEstado->count() > 0)
    <div class="section">
        <div class="section-title">📊 Ventas por Estado</div>
        <table class="table small-table">
            <thead>
                <tr>
                    <th>Estado del Pedido</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventasPorEstado as $estado => $datos)
                <tr>
                    <td class="font-weight-bold">{{ ucfirst(str_replace('_', ' ', $estado)) }}</td>
                    <td class="text-center number">{{ $datos['cantidad'] }}</td>
                    <td class="text-right currency">${{ number_format(to_float($datos['total']), 0, ',', '.') }}</td>
                    <td class="text-right">{{ to_float($stats['total_ingresos']) > 0 ? number_format((to_float($datos['total']) / to_float($stats['total_ingresos'])) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>
            <strong>Arepa la Llanerita</strong> - Sistema de Gestión de Ventas |
            Reporte generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}
        </div>
        <div style="margin-top: 5px;">
            Este reporte contiene información confidencial y está destinado únicamente para uso interno de la empresa.
        </div>
    </div>
</body>
</html>