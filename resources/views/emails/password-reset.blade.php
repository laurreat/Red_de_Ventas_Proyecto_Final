<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecimiento de Contraseña - Arepa la Llanerita</title>
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
        }
        .message {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .reset-button {
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
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,123,255,0.4);
        }
        .security-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .security-notice h3 {
            color: #856404;
            margin: 0 0 10px;
            font-size: 16px;
        }
        .security-notice p {
            color: #856404;
            margin: 0;
            font-size: 14px;
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
        .token-info {
            background-color: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #004085;
        }
        .manual-link {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">🔐</div>
            <h1>Restablecimiento de Contraseña</h1>
            <p>Arepa la Llanerita - Sistema de Ventas</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                ¡Hola {{ $user->name }} {{ $user->apellidos }}!
            </div>

            <div class="message">
                Has solicitado restablecer tu contraseña para acceder a tu cuenta en <strong>Arepa la Llanerita</strong>.
                Para continuar con el proceso, haz clic en el botón de abajo:
            </div>

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">
                    Restablecer Mi Contraseña
                </a>
            </div>

            <div class="security-notice">
                <h3>⚠️ Aviso de Seguridad</h3>
                <p>
                    Este enlace es válido por <strong>1 hora</strong> a partir del momento en que lo solicitaste.
                    Si no solicitaste este restablecimiento, puedes ignorar este correo.
                </p>
            </div>

            <div class="token-info">
                <strong>Información técnica:</strong><br>
                Enlace generado el: {{ now()->format('d/m/Y H:i:s') }}<br>
                Expira el: {{ now()->addHour()->format('d/m/Y H:i:s') }}
            </div>

            <div class="message">
                Si el botón no funciona, puedes copiar y pegar el siguiente enlace en tu navegador:
            </div>

            <div class="manual-link">
                {{ $resetUrl }}
            </div>

            <div class="message">
                <strong>¿Necesitas ayuda?</strong><br>
                Si tienes problemas para restablecer tu contraseña, contacta a nuestro equipo de soporte.
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
                    Este es un correo automático, por favor no respondas a este mensaje.<br>
                    El sabor auténtico de los llanos colombianos.
                </p>
            </div>
        </div>
    </div>
</body>
</html>