<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Referido Registrado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #8B0000 0%, #DC143C 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin: -30px -30px 30px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 20px 0;
        }
        .greeting {
            font-size: 18px;
            color: #8B0000;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .message {
            margin-bottom: 25px;
            font-size: 16px;
        }
        .referido-info {
            background-color: #f8f9fa;
            border-left: 4px solid #8B0000;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .referido-info h3 {
            margin-top: 0;
            color: #8B0000;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            width: 140px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .benefits {
            background-color: #fff9e6;
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .benefits h3 {
            margin-top: 0;
            color: #8B0000;
            font-size: 16px;
        }
        .benefits ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .benefits li {
            margin: 8px 0;
            color: #333;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #8B0000 0%, #DC143C 100%);
            color: white !important;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .highlight {
            color: #8B0000;
            font-weight: bold;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .header {
                margin: -20px -20px 20px -20px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">🎉</div>
            <h1>¡Nuevo Referido Registrado!</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                ¡Hola {{ $referrer->name }}!
            </div>
            
            <div class="message">
                <p>Tenemos excelentes noticias para ti. Una nueva persona se ha registrado usando tu código de referido y ahora forma parte de tu red.</p>
                
                <p><strong>¡Felicitaciones!</strong> Estás construyendo tu red de referidos exitosamente.</p>
            </div>
            
            <div class="referido-info">
                <h3>📋 Información del Nuevo Referido</h3>
                <div class="info-row">
                    <div class="info-label">Nombre:</div>
                    <div class="info-value">{{ $newUser->name }} {{ $newUser->apellidos }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $newUser->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Teléfono:</div>
                    <div class="info-value">{{ $newUser->telefono }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ciudad:</div>
                    <div class="info-value">{{ $newUser->ciudad }}, {{ $newUser->departamento }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Registro:</div>
                    <div class="info-value">{{ $newUser->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            
            <div class="benefits">
                <h3>💰 ¿Qué significa esto para ti?</h3>
                <ul>
                    <li>Por cada compra que realice este referido, <span class="highlight">ganarás una comisión automáticamente</span></li>
                    <li>Podrás ver el detalle de sus pedidos y tus ganancias en tu panel de referidos</li>
                    <li>Mientras más compre, más ganas tú</li>
                    <li>Tu red crece y también tus beneficios</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('cliente.referidos.detalle', ['id' => $newUser->_id]) }}" class="cta-button">
                    👁️ Ver Detalle del Referido
                </a>
            </div>
            
            <div class="message">
                <p><strong>Sigue compartiendo tu link de referido</strong> para seguir creciendo tu red y aumentando tus ganancias.</p>
                
                <p>Tu código de referido es: <span class="highlight" style="font-size: 18px;">{{ $referrer->codigo_referido }}</span></p>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Arepa la Llanerita</strong></p>
            <p>Este es un correo automático, por favor no responder.</p>
            <p style="font-size: 12px; color: #999; margin-top: 10px;">
                Si tienes alguna pregunta, contacta con nuestro equipo de soporte.
            </p>
        </div>
    </div>
</body>
</html>
