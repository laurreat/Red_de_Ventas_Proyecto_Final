<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - {{ $user->name }}</title>
    <style>
        /* Reset y configuración base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            font-size: 12px;
        }

        /* Colores de marca */
        :root {
            --primary-color: #722f37;
            --secondary-color: #8b3c44;
            --accent-color: #f8f9fa;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
        }

        /* Header del documento */
        .header {
            background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Información personal */
        .info-personal {
            background: #f8f9fa;
            border: 2px solid #722f37;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .info-personal h2 {
            color: #722f37;
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #722f37;
            padding-bottom: 8px;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
            border-bottom: 1px solid #dee2e6;
        }

        .info-label, .info-value {
            display: table-cell;
            padding: 8px 12px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #722f37;
            width: 35%;
            background: #fff;
        }

        .info-value {
            width: 65%;
            background: #fff;
        }

        /* Estadísticas */
        .stats-container {
            margin-bottom: 30px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .stats-row {
            display: table-row;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
        }

        .stat-inner {
            background: #fff;
            border: 2px solid #722f37;
            border-radius: 8px;
            padding: 20px 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #722f37;
            display: block;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Secciones de contenido */
        .section {
            margin-bottom: 25px;
            break-inside: avoid;
        }

        .section-header {
            background: #722f37;
            color: white;
            padding: 12px 20px;
            border-radius: 8px 8px 0 0;
            font-size: 16px;
            font-weight: bold;
        }

        .section-content {
            border: 2px solid #722f37;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 20px;
            background: white;
        }

        /* Tablas */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: bold;
            color: #722f37;
            text-align: left;
        }

        .data-table td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            font-size: 10px;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* Badges y estados */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }

        .badge-success { background: #28a745; }
        .badge-warning { background: #ffc107; color: #212529; }
        .badge-danger { background: #dc3545; }
        .badge-info { background: #17a2b8; }
        .badge-secondary { background: #6c757d; }
        .badge-primary { background: #007bff; }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f8f9fa;
            border-top: 2px solid #722f37;
            padding: 15px;
            font-size: 10px;
            color: #6c757d;
            text-align: center;
        }

        /* Información de documento */
        .doc-info {
            background: #e9ecef;
            border-left: 4px solid #722f37;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 11px;
        }

        /* Utilidades */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #6c757d; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        .font-bold { font-weight: bold; }

        /* Evitar salto de página */
        .no-break {
            page-break-inside: avoid;
        }

        /* Espaciado para nueva página */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header Principal -->
    <div class="header">
        <h1>🥞 AREPA LA LLANERITA</h1>
        <p>Reporte de Perfil de Usuario - {{ $fecha_generacion->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Información del documento -->
    <div class="doc-info">
        <strong>📄 Información del Documento:</strong><br>
        <strong>Usuario:</strong> {{ $user->name }} {{ $user->apellidos }}<br>
        <strong>Generado:</strong> {{ $fecha_generacion->format('d/m/Y \a \l\a\s H:i:s') }}<br>
        <strong>Tipo:</strong> Reporte completo de perfil de usuario<br>
        <strong>Versión:</strong> {{ config('app.version', '1.0') }}
    </div>

    <!-- Información Personal -->
    <div class="info-personal no-break">
        <h2>👤 Información Personal</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre Completo</div>
                <div class="info-value">{{ $user->name }} {{ $user->apellidos }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Cédula</div>
                <div class="info-value">{{ $user->cedula ?? 'No especificada' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Teléfono</div>
                <div class="info-value">{{ $user->telefono ?? 'No especificado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dirección</div>
                <div class="info-value">{{ $user->direccion ?? 'No especificada' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ciudad/Departamento</div>
                <div class="info-value">{{ $user->ciudad ?? 'No especificada' }} - {{ $user->departamento ?? 'No especificado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Nacimiento</div>
                <div class="info-value">
                    @if($user->fecha_nacimiento)
                        {{ $user->fecha_nacimiento->format('d/m/Y') }}
                        ({{ $user->fecha_nacimiento->age }} años)
                    @else
                        No especificada
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Rol en el Sistema</div>
                <div class="info-value">
                    <span class="badge badge-{{ $user->rol === 'administrador' ? 'success' : ($user->rol === 'vendedor' ? 'warning' : 'info') }}">
                        {{ ucfirst($user->rol) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado de Cuenta</div>
                <div class="info-value">
                    <span class="badge badge-{{ $user->activo ? 'success' : 'danger' }}">
                        {{ $user->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Código de Referido</div>
                <div class="info-value">{{ $user->codigo_referido ?? 'No asignado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Registro</div>
                <div class="info-value">{{ $stats['fecha_registro']->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Último Acceso</div>
                <div class="info-value">{{ $stats['ultimo_acceso']->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="stats-container no-break">
        <h2 style="color: #722f37; margin-bottom: 20px; border-bottom: 2px solid #722f37; padding-bottom: 8px;">
            📊 Estadísticas de Actividad
        </h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-inner">
                        <span class="stat-number">{{ $stats['pedidos_como_cliente'] }}</span>
                        <div class="stat-label">Pedidos como Cliente</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-inner">
                        <span class="stat-number">{{ $stats['pedidos_como_vendedor'] }}</span>
                        <div class="stat-label">Pedidos como Vendedor</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-inner">
                        <span class="stat-number">{{ $stats['total_referidos'] }}</span>
                        <div class="stat-label">Total Referidos</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-inner">
                        <span class="stat-number">${{ number_format($stats['comisiones_disponibles'], 0) }}</span>
                        <div class="stat-label">Comisiones Disponibles</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Financieras -->
    <div class="section no-break">
        <div class="section-header">💰 Resumen Financiero</div>
        <div class="section-content">
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-inner">
                            <span class="stat-number">${{ number_format($stats['total_gastado'], 0) }}</span>
                            <div class="stat-label">Total Gastado</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-inner">
                            <span class="stat-number">${{ number_format($stats['total_vendido'], 0) }}</span>
                            <div class="stat-label">Total Vendido</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-inner">
                            <span class="stat-number">${{ number_format($stats['comisiones_ganadas'], 0) }}</span>
                            <div class="stat-label">Comisiones Ganadas</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-inner">
                            <span class="stat-number">${{ number_format($user->meta_mensual ?? 0, 0) }}</span>
                            <div class="stat-label">Meta Mensual</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos Recientes -->
    @if($pedidos_recientes->count() > 0)
    <div class="section">
        <div class="section-header">🛒 Pedidos Recientes (Últimos 10)</div>
        <div class="section-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Número</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Cliente/Vendedor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos_recientes as $pedido)
                    <tr>
                        <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                        <td>{{ $pedido->numero_pedido ?? '#' . substr((string)$pedido->_id, -6) }}</td>
                        <td>
                            @if($pedido->user_id == $user->_id)
                                <span class="badge badge-info">Cliente</span>
                            @else
                                <span class="badge badge-warning">Vendedor</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = $pedido->estado === 'entregado' ? 'success' :
                                            ($pedido->estado === 'cancelado' ? 'danger' : 'warning');
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">
                                {{ ucfirst($pedido->estado ?? 'pendiente') }}
                            </span>
                        </td>
                        <td>${{ number_format($pedido->total_final ?? 0, 0) }}</td>
                        <td>
                            @if($pedido->user_id == $user->_id && isset($pedido->vendedor_data))
                                V: {{ $pedido->vendedor_data['name'] ?? 'Sin vendedor' }}
                            @elseif(isset($pedido->cliente_data))
                                C: {{ $pedido->cliente_data['name'] ?? 'Cliente eliminado' }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Referidos Recientes -->
    @if($referidos_recientes->count() > 0)
    <div class="section page-break">
        <div class="section-header">👥 Referidos Recientes (Últimos 10)</div>
        <div class="section-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha de Registro</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referidos_recientes as $referido)
                    <tr>
                        <td>{{ $referido->created_at->format('d/m/Y') }}</td>
                        <td>{{ $referido->name }} {{ $referido->apellidos }}</td>
                        <td>{{ $referido->email }}</td>
                        <td>
                            @php
                                $roleColor = [
                                    'administrador' => 'success',
                                    'lider' => 'info',
                                    'vendedor' => 'warning',
                                    'cliente' => 'secondary'
                                ][$referido->rol] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $roleColor }}">
                                {{ ucfirst($referido->rol) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $referido->activo ? 'success' : 'danger' }}">
                                {{ $referido->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>{{ $referido->telefono ?? 'No especificado' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Información adicional -->
    <div class="section">
        <div class="section-header">ℹ️ Información Adicional</div>
        <div class="section-content">
            <p><strong>Biografía:</strong> {{ $user->bio ?? 'Sin información adicional proporcionada.' }}</p>

            @if($user->referido_por)
                @php
                    $referidor = \App\Models\User::find($user->referido_por);
                @endphp
                <p><strong>Referido por:</strong>
                    {{ $referidor ? $referidor->name . ' ' . $referidor->apellidos : 'Usuario no encontrado' }}
                </p>
            @endif

            <div class="mt-2">
                <strong>Configuración de Notificaciones:</strong><br>
                <small>
                    📧 Email pedidos: {{ ($user->notif_email_pedidos ?? true) ? 'Activo' : 'Inactivo' }} |
                    👥 Email usuarios: {{ ($user->notif_email_usuarios ?? true) ? 'Activo' : 'Inactivo' }} |
                    🔔 Push browser: {{ ($user->notif_push_browser ?? true) ? 'Activo' : 'Inactivo' }}
                </small>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <strong>Arepa la Llanerita</strong> - Sistema de Ventas y Gestión<br>
        Documento generado el {{ $fecha_generacion->format('d/m/Y \a \l\a\s H:i:s') }} |
        Página <span class="pagenum"></span> de <span class="pagecount"></span> |
        <strong>Confidencial:</strong> Este documento contiene información personal y debe ser tratado de manera confidencial.
    </div>
</body>
</html>