<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu Email</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #18181b;
            color: #ffffff;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 40px 30px;
            text-align: center;
            border-radius: 12px 12px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            color: #000000;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            background-color: #27272a;
            padding: 40px 30px;
            border-radius: 0 0 12px 12px;
        }
        .welcome-text {
            font-size: 18px;
            margin-bottom: 20px;
            color: #ffffff;
        }
        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #d4d4d8;
            margin-bottom: 30px;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .verify-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #000000;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        .verify-button:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
        .info-box {
            background-color: #3f3f46;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #d4d4d8;
        }
        .alternative-link {
            background-color: #3f3f46;
            padding: 15px;
            border-radius: 8px;
            margin-top: 25px;
            word-break: break-all;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #a1a1aa;
        }
        .alternative-link a {
            color: #fbbf24;
            text-decoration: none;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            padding: 30px 20px;
            color: #71717a;
            font-size: 14px;
        }
        .footer a {
            color: #f59e0b;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .header, .content {
                padding: 30px 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .verify-button {
                padding: 14px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">✉️</div>
            <h1>Verifica tu Correo Electrónico</h1>
        </div>
        
        <div class="content">
            <p class="welcome-text">¡Hola, {{ $user->name }}! 👋</p>
            
            <p class="message">
                ¡Bienvenido/a a <strong>Miss Sweet Candy</strong>! Estamos emocionados de tenerte con nosotros.
            </p>
            
            <p class="message">
                Para completar tu registro y comenzar a disfrutar de nuestras promociones exclusivas, 
                solo necesitas verificar tu dirección de correo electrónico haciendo clic en el botón de abajo:
            </p>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    ✓ Verificar mi Correo
                </a>
            </div>
            
            <div class="info-box">
                <p>
                    <strong>⏰ Importante:</strong> Este enlace expirará en 60 minutos por seguridad.
                </p>
            </div>
            
            <p class="message">
                Una vez verificado tu correo, podrás:
            </p>
            <ul style="color: #d4d4d8; line-height: 1.8; margin-left: 20px;">
                <li>Recibir notificaciones de promociones exclusivas 🎁</li>
                <li>Acceder a ofertas y descuentos especiales 💰</li>
                <li>Realizar pedidos online 🛒</li>
                <li>Acumular puntos y recompensas ⭐</li>
            </ul>
            
            <div class="alternative-link">
                <p><strong>¿El botón no funciona?</strong> Copia y pega este enlace en tu navegador:</p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>
            
            <div class="info-box" style="margin-top: 30px; border-left-color: #ef4444;">
                <p>
                    <strong>⚠️ ¿No solicitaste esta cuenta?</strong><br>
                    Si no creaste una cuenta con nosotros, por favor ignora este correo.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>
                <strong>Miss Sweet Candy</strong><br>
                Tu cafetería favorita 🍰☕<br>
                <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                Este es un correo automático, por favor no respondas a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
