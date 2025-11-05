<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - Miss Sweet Candy</title>
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
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ff9500 0%, #ff6b00 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .header-text h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header-text p {
            color: #999;
            font-size: 14px;
        }

        .btn-new {
            background: linear-gradient(135deg, #ff9500 0%, #ff6b00 100%);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 149, 0, 0.4);
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            border-radius: 12px;
        }

        .tab {
            flex: 1;
            padding: 12px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: #999;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: rgba(255, 149, 0, 0.2);
            color: #ff9500;
        }

        .reservas-list {
            display: grid;
            gap: 15px;
        }

        .reserva-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            border-left: 4px solid #ff9500;
            transition: all 0.3s ease;
        }

        .reserva-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }

        .reserva-card.cancelada {
            border-left-color: #ef4444;
            opacity: 0.6;
        }

        .reserva-card.cumplida {
            border-left-color: #22c55e;
        }

        .reserva-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .reserva-code {
            font-size: 18px;
            font-weight: bold;
            color: #ff9500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmada {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #fff;
        }

        .status-pendiente {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff;
        }

        .status-cancelada {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .status-cumplida {
            background: rgba(100, 116, 139, 0.2);
            color: #94a3b8;
        }

        .reserva-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-icon {
            font-size: 18px;
        }

        .detail-text {
            font-size: 14px;
        }

        .detail-label {
            color: #999;
            font-size: 12px;
        }

        .detail-value {
            color: #fff;
            font-weight: 600;
        }

        .reserva-actions {
            display: flex;
            gap: 10px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-action {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
        }

        .btn-modify {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .btn-modify:hover {
            background: rgba(59, 130, 246, 0.3);
        }

        .btn-cancel {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .btn-cancel:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        .btn-details {
            background: rgba(255, 149, 0, 0.2);
            color: #ff9500;
        }

        .btn-details:hover {
            background: rgba(255, 149, 0, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
        }

        .empty-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .empty-text {
            color: #999;
            margin-bottom: 30px;
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

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <div class="header-icon">📋</div>
                <div class="header-text">
                    <h1>Mis Reservas</h1>
                    <p>Gestiona tus reservas activas</p>
                </div>
            </div>
            <a href="{{ route('reservas.create') }}" class="btn-new">
                <span>➕</span>
                <span>Nueva Reserva</span>
            </a>
        </div>

        <!-- Mostrar mensajes -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="tabs">
            <button class="tab active" onclick="filterReservas('activas')">
                Activas ({{ $reservas->whereIn('estado', ['pendiente', 'confirmada'])->count() }})
            </button>
            <button class="tab" onclick="filterReservas('historial')">
                Historial ({{ $reservas->whereIn('estado', ['cancelada', 'cumplida'])->count() }})
            </button>
        </div>

        <div class="reservas-list" id="reservasList">
            @php
                $reservasActivas = $reservas->whereIn('estado', ['pendiente', 'confirmada']);
            @endphp

            @if($reservasActivas->count() > 0)
                @foreach($reservasActivas as $reserva)
                    <div class="reserva-card" data-estado="{{ $reserva->estado }}">
                        <div class="reserva-header">
                            <div class="reserva-code">{{ $reservaService->generarCodigoReserva($reserva) }}</div>
                            <span class="status-badge status-{{ $reserva->estado }}">
                                {{ ucfirst($reserva->estado) }}
                            </span>
                        </div>

                        <div class="reserva-details">
                            <div class="detail-item">
                                <span class="detail-icon">📅</span>
                                <div class="detail-text">
                                    <div class="detail-label">Fecha</div>
                                    <div class="detail-value">{{ $reserva->fecha->format('d M Y') }}</div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <span class="detail-icon">🕐</span>
                                <div class="detail-text">
                                    <div class="detail-label">Hora</div>
                                    <div class="detail-value">{{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}</div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <span class="detail-icon">👥</span>
                                <div class="detail-text">
                                    <div class="detail-label">Personas</div>
                                    <div class="detail-value">{{ $reserva->numero_personas }} comensales</div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <span class="detail-icon">🪑</span>
                                <div class="detail-text">
                                    <div class="detail-label">Mesa</div>
                                    <div class="detail-value">{{ $reserva->mesa->nombre }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="reserva-actions">
                            <a href="{{ route('reservas.show', $reserva) }}" class="btn-action btn-details">
                                👁️ Ver Detalles
                            </a>
                            <a href="{{ route('reservas.edit', $reserva) }}" class="btn-action btn-modify">
                                ✏️ Modificar
                            </a>
                            <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" style="display: contents;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-cancel" onclick="return confirm('¿Estás seguro de que deseas cancelar esta reserva?')">
                                    ❌ Cancelar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <div class="empty-title">No tienes reservas activas</div>
                    <div class="empty-text">Cuando hagas una reserva, aparecerá aquí para que puedas gestionarla.</div>
                    <a href="{{ route('reservas.create') }}" class="btn-new">
                        <span>➕</span>
                        <span>Hacer mi primera reserva</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function filterReservas(tipo) {
            // Remover active de todos los tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Activar el tab clickeado
            event.target.classList.add('active');

            const todasLasReservas = document.querySelectorAll('.reserva-card');
            
            todasLasReservas.forEach(reserva => {
                if (tipo === 'activas') {
                    const estado = reserva.getAttribute('data-estado');
                    reserva.style.display = (estado === 'pendiente' || estado === 'confirmada') ? 'block' : 'none';
                } else if (tipo === 'historial') {
                    const estado = reserva.getAttribute('data-estado');
                    reserva.style.display = (estado === 'cancelada' || estado === 'cumplida') ? 'block' : 'none';
                }
            });
        }
    </script>
</body>
</html>