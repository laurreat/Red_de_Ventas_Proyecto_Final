<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        @page {
            margin: 1.5cm 1cm;
            @top-center {
                content: "{{ $titulo }} - Arepa la Llanerita";
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
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #722F37;
        }

        .logo {
            color: #722F37;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .export-info {
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
            font-size: 10px;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 20px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #722F37 0%, #8B3A42 100%);
            color: white;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(114, 47, 55, 0.2);
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 8px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #722F37;
            margin: 20px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #722F37;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-size: 9px;
        }

        .table th {
            background: linear-gradient(135deg, #722F37 0%, #8B3A42 100%);
            color: white;
            font-weight: bold;
            padding: 8px 5px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table td {
            padding: 6px 5px;
            border-bottom: 1px solid #e9ecef;
            font-size: 8px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #fff3cd;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            min-width: 30px;
        }

        .badge-primary {
            background-color: #007bff;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        .summary-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            border: 1px solid #dee2e6;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 10px;
            color: #722F37;
        }

        .footer {
            position: fixed;
            bottom: 0.5cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
        }

        .no-data-icon {
            font-size: 36px;
            margin-bottom: 12px;
            color: #ccc;
        }

        .nivel-indicator {
            font-size: 7px;
            background: #e9ecef;
            padding: 1px 4px;
            border-radius: 3px;
            color: #495057;
        }

        .referidos-count {
            background: #28a745;
            color: white;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }

        .hierarchy-section {
            background: #f1f3f4;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #722F37;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🫓 AREPA LA LLANERITA</div>
        <div class="subtitle">{{ $titulo }}</div>
        <div class="export-info">
            Exportado el {{ $stats['fecha_exportacion'] }}
            @if($cedula_filtro)
                | Filtrado por cédula: {{ $cedula_filtro }}
            @endif
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_usuarios'] }}</div>
            <div class="stat-label">Total Usuarios</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_vendedores'] }}</div>
            <div class="stat-label">Vendedores</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_lideres'] }}</div>
            <div class="stat-label">Líderes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['usuarios_con_referidos'] }}</div>
            <div class="stat-label">Con Referidos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['usuarios_sin_referidor'] }}</div>
            <div class="stat-label">Sin Referidor</div>
        </div>
    </div>

    <!-- Tabla de Red de Referidos -->
    <div class="section-title">👥 Detalle de la Red de Referidos</div>

    @if($referidos->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 18%">Usuario</th>
                    <th style="width: 15%">Contacto</th>
                    <th style="width: 8%">Rol</th>
                    <th style="width: 15%">Referidor</th>
                    <th style="width: 8%">Ref. Directos</th>
                    <th style="width: 10%">Código</th>
                    <th style="width: 12%">Registro</th>
                    <th style="width: 8%">Estado</th>
                    <th style="width: 6%">Nivel</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referidos as $index => $referido)
                <tr>
                    <td class="font-bold">{{ $referido['Nombre'] }}</td>
                    <td>
                        <div>{{ $referido['Email'] }}</div>
                        @if($referido['Teléfono'] !== 'N/A')
                            <div style="font-size: 7px; color: #666;">{{ $referido['Teléfono'] }}</div>
                        @endif
                        @if($referido['Cédula'] !== 'N/A')
                            <div style="font-size: 7px; color: #666;">CC: {{ $referido['Cédula'] }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($referido['Rol'] === 'Lider')
                            <span class="badge badge-warning">{{ $referido['Rol'] }}</span>
                        @else
                            <span class="badge badge-primary">{{ $referido['Rol'] }}</span>
                        @endif
                    </td>
                    <td>
                        @if($referido['Referidor'] !== 'Sin referidor')
                            <div style="font-size: 8px;">{{ $referido['Referidor'] }}</div>
                        @else
                            <span style="color: #6c757d; font-style: italic;">{{ $referido['Referidor'] }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="referidos-count">{{ $referido['Referidos Directos'] }}</span>
                    </td>
                    <td class="text-center">
                        <code style="background: #e9ecef; padding: 1px 3px; border-radius: 2px; font-size: 7px;">
                            {{ $referido['Código Referido'] }}
                        </code>
                    </td>
                    <td style="font-size: 7px;">{{ $referido['Fecha Registro'] }}</td>
                    <td class="text-center">
                        @if($referido['Estado'] === 'Activo')
                            <span class="status-active">●</span>
                        @else
                            <span class="status-inactive">●</span>
                        @endif
                        <span style="font-size: 7px;">{{ $referido['Estado'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="nivel-indicator">{{ $index + 1 }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Análisis de la Red -->
        <div class="summary-section">
            <div class="section-title" style="margin-top: 0;">📊 Análisis de la Red</div>
            <div class="summary-grid">
                <div>
                    <div class="summary-item">
                        <span>Total de usuarios:</span>
                        <span class="font-bold">{{ $stats['total_usuarios'] }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Tasa de liderazgo:</span>
                        <span class="font-bold">{{ $stats['total_usuarios'] > 0 ? number_format(($stats['total_lideres'] / $stats['total_usuarios']) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
                <div>
                    <div class="summary-item">
                        <span>Usuarios activos:</span>
                        <span class="font-bold">{{ $referidos->where('Estado', 'Activo')->count() }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Con referidos:</span>
                        <span class="font-bold">{{ $stats['usuarios_con_referidos'] }}</span>
                    </div>
                </div>
                <div>
                    <div class="summary-item">
                        <span>Promedio referidos:</span>
                        <span class="font-bold">{{ $stats['usuarios_con_referidos'] > 0 ? number_format($referidos->sum('Referidos Directos') / $stats['usuarios_con_referidos'], 1) : 0 }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Cobertura de red:</span>
                        <span class="font-bold">{{ $stats['total_usuarios'] > 0 ? number_format(($stats['usuarios_con_referidos'] / $stats['total_usuarios']) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        @if($stats['usuarios_con_referidos'] > 0)
        <div class="hierarchy-section">
            <h6 style="color: #722F37; margin-bottom: 8px; font-size: 10px;">💡 Insights de la Red:</h6>
            <ul style="font-size: 8px; color: #495057; margin-left: 15px;">
                <li>{{ $stats['total_lideres'] }} líderes están gestionando equipos de referidos</li>
                <li>{{ $referidos->where('Referidos Directos', '>', 0)->count() }} usuarios han logrado referir con éxito</li>
                <li>El {{ number_format((($stats['total_usuarios'] - $stats['usuarios_sin_referidor']) / $stats['total_usuarios']) * 100, 1) }}% de los usuarios están conectados a la red</li>
                @if($stats['usuarios_sin_referidor'] > 0)
                <li style="color: #dc3545;">{{ $stats['usuarios_sin_referidor'] }} usuarios requieren asignación de referidor</li>
                @endif
            </ul>
        </div>
        @endif

    @else
        <div class="no-data">
            <div class="no-data-icon">👥</div>
            <h3>No hay datos de red disponibles</h3>
            <p>No se encontraron usuarios en la red de referidos para exportar.</p>
        </div>
    @endif

    <div class="footer">
        <div>Arepa la Llanerita - Sistema MLM de Red de Referidos</div>
        <div>Exportado el {{ $stats['fecha_exportacion'] }} | Documento confidencial</div>
    </div>
</body>
</html>