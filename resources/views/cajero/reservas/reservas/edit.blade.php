<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Reserva - Miss Sweet Candy</title>
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
            max-width: 600px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
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

        .form-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .form-section {
            margin-bottom: 25px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            color: #ff9500;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 15px;
            background: rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #ff9500;
            background: rgba(0, 0, 0, 0.5);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .date-time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .counter-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 10px;
        }

        .counter-btn {
            width: 40px;
            height: 40px;
            background: #ff9500;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .counter-btn:hover {
            background: #ff6b00;
            transform: scale(1.1);
        }

        .counter-value {
            font-size: 24px;
            font-weight: bold;
            color: #ff9500;
        }

        .availability-card {
            background: rgba(34, 197, 94, 0.1);
            border: 2px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            display: none;
        }

        .availability-card.show {
            display: block;
        }

        .availability-card .icon {
            font-size: 20px;
            margin-right: 10px;
        }

        .availability-text {
            color: #22c55e;
            font-weight: 600;
        }

        .current-reservation {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .current-reservation h3 {
            color: #3b82f6;
            margin-bottom: 10px;
        }

        .reservation-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .btn-primary {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #ff9500 0%, #ff6b00 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 149, 0, 0.4);
        }

        .btn-secondary {
            width: 100%;
            padding: 18px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            border-color: #ff9500;
            background: rgba(255, 149, 0, 0.1);
        }

        .info-card {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .info-card p {
            color: #93c5fd;
            font-size: 14px;
            line-height: 1.6;
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
            <div class="header-icon">✏️</div>
            <div class="header-text">
                <h1>Modificar Reserva</h1>
                <p>Actualiza los datos de tu reserva</p>
            </div>
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

        <!-- Reserva actual -->
        <div class="current-reservation">
            <h3>Reserva Actual</h3>
            <div class="reservation-detail">
                <span>Mesa:</span>
                <strong>{{ $reserva->mesa->nombre }}</strong>
            </div>
            <div class="reservation-detail">
                <span>Fecha y Hora:</span>
                <strong>{{ $reserva->fecha->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}</strong>
            </div>
            <div class="reservation-detail">
                <span>Personas:</span>
                <strong>{{ $reserva->numero_personas }} comensales</strong>
            </div>
        </div>

        <form action="{{ route('reservas.update', $reserva) }}" method="POST" id="reservaForm">
            @csrf
            @method('PUT')
            
            <div class="form-card">
                <div class="form-section">
                    <label class="form-label">📅 Nueva Fecha y Hora</label>
                    <div class="date-time-grid">
                        <input type="date" class="form-input" id="fecha" name="fecha" 
                               value="{{ $reserva->fecha->format('Y-m-d') }}" 
                               min="{{ date('Y-m-d') }}" required>
                        <input type="time" class="form-input" id="hora" name="hora" 
                               value="{{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}" required>
                    </div>
                </div>

                <div class="form-section">
                    <label class="form-label">👥 Número de Personas</label>
                    <div class="counter-control">
                        <button type="button" class="counter-btn" onclick="decrementPersonas()">−</button>
                        <span class="counter-value" id="personas">{{ $reserva->numero_personas }}</span>
                        <input type="hidden" id="numero_personas" name="numero_personas" value="{{ $reserva->numero_personas }}">
                        <button type="button" class="counter-btn" onclick="incrementPersonas()">+</button>
                    </div>
                </div>

                <button type="button" class="btn-primary" onclick="verificarDisponibilidad()">
                    🔍 Verificar Nueva Disponibilidad
                </button>

                <div class="availability-card" id="availabilityCard">
                    <div style="display: flex; align-items: center;">
                        <span class="icon">✅</span>
                        <span class="availability-text">¡Tu mesa está disponible en la nueva fecha/hora!</span>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-section">
                    <label class="form-label">💬 Observaciones (Opcional)</label>
                    <textarea class="form-textarea" name="observaciones" placeholder="Ej: Celebración de cumpleaños, preferencia de ubicación...">{{ $reserva->observaciones }}</textarea>
                </div>

                <div class="info-card">
                    <p><strong>📋 Importante:</strong></p>
                    <p>• Solo puedes modificar reservas activas (pendientes o confirmadas)</p>
                    <p>• La modificación está sujeta a disponibilidad</p>
                    <p>• Si cambias la fecha/hora, se verificará que tu mesa esté disponible</p>
                </div>

                <button type="submit" class="btn-primary" id="btnActualizar">
                    💾 Actualizar Reserva
                </button>
                <a href="{{ route('reservas.show', $reserva) }}" class="btn-secondary">
                    ← Volver a Detalles
                </a>
            </div>
        </form>
    </div>

    <script>
        let personasCount = {{ $reserva->numero_personas }};
        let disponibilidadVerificada = false;

        function incrementPersonas() {
            if (personasCount < 10) {
                personasCount++;
                updatePersonasCounter();
            }
        }

        function decrementPersonas() {
            if (personasCount > 1) {
                personasCount--;
                updatePersonasCounter();
            }
        }

        function updatePersonasCounter() {
            document.getElementById('personas').textContent = personasCount;
            document.getElementById('numero_personas').value = personasCount;
            disponibilidadVerificada = false;
            document.getElementById('btnActualizar').disabled = true;
        }

        async function verificarDisponibilidad() {
            const fecha = document.getElementById('fecha').value;
            const hora = document.getElementById('hora').value;
            const personas = document.getElementById('numero_personas').value;

            if (!fecha || !hora) {
                alert('Por favor completa la fecha y hora');
                return;
            }

            try {
                const response = await fetch('{{ route("reservas.verificar-disponibilidad") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        fecha: fecha,
                        hora: hora,
                        numero_personas: personas,
                        reserva_actual_id: {{ $reserva->id }} // Para excluir la reserva actual
                    })
                });

                const data = await response.json();

                if (data.disponible) {
                    document.getElementById('availabilityCard').classList.add('show');
                    document.getElementById('btnActualizar').disabled = false;
                    disponibilidadVerificada = true;
                } else {
                    alert('No hay disponibilidad para la nueva fecha y hora seleccionadas. Por favor intenta con otra fecha/hora.');
                    document.getElementById('btnActualizar').disabled = true;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al verificar disponibilidad. Por favor intenta nuevamente.');
            }
        }

        // Validar formulario antes de enviar
        document.getElementById('reservaForm').addEventListener('submit', function(e) {
            if (!disponibilidadVerificada) {
                e.preventDefault();
                alert('Por favor verifica la disponibilidad antes de actualizar la reserva.');
                return;
            }
        });

        // Establecer fecha mínima como hoy
        document.getElementById('fecha').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>