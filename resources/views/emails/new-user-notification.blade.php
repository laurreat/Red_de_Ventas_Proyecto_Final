<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Registro de Usuario - Arepa la Llanerita</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #343a40;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.7;
        }
        .user-info {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .user-info h3 {
            color: #28a745;
            margin: 0 0 15px;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 160px;
        }
        .info-value {
            color: #6c757d;
            flex: 1;
        }
        .stats-container {
            display: flex;
            gap: 15px;
            margin: 30px 0;
        }
        .stat-box {
            flex: 1;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .view-button {
            display: inline-block;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        .view-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,123,255,0.4);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .company-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .highlight p {
            color: #856404;
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">👤</div>
            <h1>Nuevo Registro de Usuario</h1>
            <p>Arepa la Llanerita - Sistema de Ventas</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                ¡Hola Administrador!
            </div>

            <div class="message">
                Te informamos que un nuevo usuario se ha registrado en el sistema <strong>Arepa la Llanerita</strong>.
                A continuación, encontrarás los detalles del nuevo registro:
            </div>

            <!-- User Information -->
            <div class="user-info">
                <h3>📋 Información del Usuario</h3>

                <div class="info-row">
                    <div class="info-label">Nombre completo:</div>
                    <div class="info-value">{{ $user->name }} {{ $user->apellidos }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Correo electrónico:</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Cédula:</div>
                    <div class="info-value">{{ $user->cedula }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Teléfono:</div>
                    <div class="info-value">{{ $user->telefono }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Ciudad:</div>
                    <div class="info-value">{{ $user->ciudad }}, {{ $user->departamento }}</div>
                </div>

                @if($user->direccion)
                <div class="info-row">
                    <div class="info-label">Dirección:</div>
                    <div class="info-value">{{ $user->direccion }}</div>
                </div>
                @endif

                <div class="info-row">
                    <div class="info-label">Fecha de nacimiento:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($user->fecha_nacimiento)->format('d/m/Y') }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Rol asignado:</div>
                    <div class="info-value">{{ ucfirst($user->rol) }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Código de referido:</div>
                    <div class="info-value"><strong>{{ $user->codigo_referido }}</strong></div>
                </div>

                @if($user->referido_por)
                <div class="info-row">
                    <div class="info-label">Referido por:</div>
                    <div class="info-value">
                        @php
                            $referidor = \App\Models\User::find($user->referido_por);
                        @endphp
                        @if($referidor)
                            {{ $referidor->name }} {{ $referidor->apellidos }} ({{ $referidor->codigo_referido }})
                        @else
                            Usuario no encontrado
                        @endif
                    </div>
                </div>
                @endif

                <div class="info-row">
                    <div class="info-label">Fecha de registro:</div>
                    <div class="info-value">{{ now()->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>

            @if($user->referido_por)
            <div class="highlight">
                <p>
                    ⭐ <strong>¡Este usuario fue referido!</strong> El sistema de referidos está funcionando correctamente.
                </p>
            </div>
            @endif

            <!-- Statistics (opcional - puedes personalizarlo) -->
            <div class="stats-container">
                <div class="stat-box success">
                    <div class="stat-number">{{ \App\Models\User::count() }}</div>
                    <div class="stat-label">Usuarios Totales</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">{{ \App\Models\User::where('created_at', '>=', now()->startOfDay())->count() }}</div>
                    <div class="stat-label">Registros Hoy</div>
                </div>
            </div>

            <div class="button-container">
                <a href="{{ url('/admin/usuarios') }}" class="view-button">
                    Ver en el Sistema
                </a>
            </div>

            <div class="message">
                <strong>Acciones recomendadas:</strong><br>
                • Verificar la información del usuario<br>
                • Confirmar que no sea un registro duplicado<br>
                • Revisar si requiere alguna acción adicional
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>Arepa la Llanerita</strong><br>
                Sistema de Ventas con Red de Referidos
            </p>

            <div class="company-info">
                <p>
                    Este es un correo automático de notificación del sistema.<br>
                    El sabor auténtico de los llanos colombianos.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
