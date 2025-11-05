<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Confirmada - Miss Sweet Candy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 500px;
            width: 100%;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .title {
            text-align: center;
            font-size: 28px;
            margin-bottom: 10px;
            color: #22c55e;
        }

        .subtitle {
            text-align: center;
            color: #999;
            margin-bottom: 30px;
        }

        .reservation-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .reservation-code {
            background: rgba(255, 149, 0, 0.1);
            border: 2px dashed #ff9500;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
        }

        .code-label {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .code-value {
            font-size: 28px;
            font-weight: bold;
            color: #ff9500;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            background: #fff;
            border-radius: 12px;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .qr-placeholder {
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                45deg,
                #000,
                #000 10px,
                #fff 10px,
                #fff 20px
            );
            border-radius: 8px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #999;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-value {
            font-weight: 600;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #fff;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .action-buttons {
            display: grid;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 16px;
            border-radius: 12px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff9500 0%, #ff6b00 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 149, 0, 0.4);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            border-color: #ff9500;
            background: rgba(255, 149, 0, 0.1);
        }

        .btn-danger {
            background: transparent;
            border: 2px solid rgba(239, 68, 68, 0.5);
            color: #ef4444;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
        }

        .notification-sent {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notification-sent .icon {
            font-size: 24px;
        }

        .notification-sent .text {
            color: #93c5fd;
            font-size: 14px;
        }

        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 2px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
        }
    </style>
</head>
<body>
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="success-icon">✓</div>
        
        <h1 class="title">¡Reserva Confirmada!</h1>
        <p class="subtitle">Tu mesa ha sido reservada exitosamente</p>

        <div class="reservation-card">
            <div class="reservation-code">
                <div class="code-label">Código de Reserva</div>
                <div class="code-value">{{ $reservaService->generarCodigoReserva($reserva) }}</div>
            </div>

            <div class="qr-code">
                <div class="qr-placeholder"></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">📅 Fecha</div>
                <div class="detail-value">{{ $reserva->fecha->format('d M Y') }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">🕐 Hora</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">👥 Personas</div>
                <div class="detail-value">{{ $reserva->numero_personas }} comensales</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">🪑 Mesa</div>
                <div class="detail-value">{{ $reserva->mesa->nombre }} (Capacidad {{ $reserva->mesa->capacidad }})</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">📍 Estado</div>
                <div class="detail-value">
                    <span class="status-badge">{{ ucfirst($reserva->estado) }}</span>
                </div>
            </div>

            @if($reserva->observaciones)
            <div class="detail-row">
                <div class="detail-label">💬 Observaciones</div>
                <div class="detail-value">{{ $reserva->observaciones }}</div>
            </div>
            @endif
        </div>

        <div class="notification-sent">
            <span class="icon">📧</span>
            <div class="text">
                <strong>Confirmación enviada</strong><br>
                Revisa tu email para más detalles y el código QR.
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-primary" onclick="addToCalendar()">
                📅 Agregar al Calendario
            </button>
            <a href="{{ route('reservas.index') }}" class="btn btn-secondary">
                📋 Ver Mis Reservas
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                🏠 Volver al Inicio
            </a>
            <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" style="display: contents;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas cancelar esta reserva?')">
                    ❌ Cancelar Reserva
                </button>
            </form>
        </div>
    </div>

    <script>
        function addToCalendar() {
            // Datos para el calendario
            const title = 'Reserva Miss Sweet Candy';
            const startDate = '{{ $reserva->fecha->format("Ymd") }}T{{ \Carbon\Carbon::parse($reserva->hora)->format("His") }}';
            const endDate = '{{ $reserva->fecha->format("Ymd") }}T{{ \Carbon\Carbon::parse($reserva->hora)->addHours(2)->format("His") }}';
            const location = 'Miss Sweet Candy';
            const details = `Reserva para {{ $reserva->numero_personas }} personas en {{ $reserva->mesa->nombre }}. Código: {{ $reservaService->generarCodigoReserva($reserva) }}`;

            // Google Calendar
            const googleUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${startDate}/${endDate}&details=${encodeURIComponent(details)}&location=${encodeURIComponent(location)}`;
            
            window.open(googleUrl, '_blank');
        }
    </script>
</body>
</html>