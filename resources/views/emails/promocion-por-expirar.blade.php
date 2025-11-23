<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promoción Por Expirar</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .warning-badge {
            display: inline-block;
            background-color: #fee2e2;
            color: #991b1b;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .promo-details {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .promo-details p {
            margin: 10px 0;
            color: #374151;
        }
        .promo-details strong {
            color: #1f2937;
        }
        .countdown {
            font-size: 48px;
            font-weight: bold;
            color: #ef4444;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ ¡Promoción Por Expirar!</h1>
        </div>
        
        <div class="content">
            <h2 style="color: #1f2937; margin-top: 0;">{{ $promocion->nombre }}</h2>
            
            <div class="warning-badge">
                ⚠️ Expira en {{ $diasRestantes }} {{ $diasRestantes == 1 ? 'día' : 'días' }}
            </div>

            <div class="countdown">
                {{ $diasRestantes }}
                <div style="font-size: 16px; color: #6b7280; font-weight: normal;">
                    {{ $diasRestantes == 1 ? 'día restante' : 'días restantes' }}
                </div>
            </div>

            <div class="promo-details">
                <p><strong>📊 Tipo:</strong> 
                    @if($promocion->tipo === 'porcentaje')
                        {{ $promocion->valor }}% de descuento
                    @elseif($promocion->tipo === 'monto_fijo')
                        ${{ number_format($promocion->valor, 2) }} de descuento
                    @elseif($promocion->tipo === '2x1')
                        2x1
                    @elseif($promocion->tipo === 'combo')
                        Combo Especial
                    @endif
                </p>

                <p><strong>📅 Fecha de expiración:</strong> 
                    {{ $promocion->fecha_fin->format('d/m/Y') }}
                </p>

                @if($promocion->hora_fin)
                <p><strong>🕐 Hora límite:</strong> {{ \Carbon\Carbon::parse($promocion->hora_fin)->format('h:i A') }}</p>
                @endif

                <p><strong>🎯 Aplica sobre:</strong> {{ ucfirst($promocion->aplica_sobre) }}</p>
            </div>

            <p style="color: #4b5563; line-height: 1.6;">
                <strong>Recordatorio:</strong> Esta promoción expirará pronto. Asegúrate de comunicarla a tus clientes para que puedan aprovecharla antes de que finalice.
            </p>

            @if(!empty($promocion->dias_semana) && count($promocion->dias_semana) > 0)
            <p style="color: #6b7280;">
                <strong>Días disponibles:</strong> {{ implode(', ', array_map('ucfirst', $promocion->dias_semana)) }}
            </p>
            @endif
        </div>

        <div class="footer">
            <p>Miss Sweet Candy - Sistema de Gestión de Cafetería</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
