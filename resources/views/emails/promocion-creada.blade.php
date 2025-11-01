<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Promoción</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
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
        .promo-badge {
            display: inline-block;
            background-color: #dcfce7;
            color: #15803d;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .promo-details {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
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
        .cta-button {
            display: inline-block;
            background-color: #f59e0b;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
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
            <h1>🎉 ¡Nueva Promoción Disponible!</h1>
        </div>
        
        <div class="content">
            <h2 style="color: #1f2937; margin-top: 0;">{{ $promocion->nombre }}</h2>
            
            <div class="promo-badge">
                @if($promocion->tipo === 'porcentaje')
                    {{ $promocion->valor }}% de descuento
                @elseif($promocion->tipo === 'monto_fijo')
                    ${{ number_format($promocion->valor, 2) }} de descuento
                @elseif($promocion->tipo === '2x1')
                    2x1 - ¡Paga 1 y lleva 2!
                @elseif($promocion->tipo === 'combo')
                    Combo Especial
                @endif
            </div>

            <div class="promo-details">
                <h3 style="color: #1f2937; margin-top: 0; font-size: 18px;">📋 Detalles de la Promoción</h3>
                
                <p><strong>📅 Vigencia:</strong> 
                    @if($promocion->fecha_inicio && $promocion->fecha_fin)
                        Del {{ $promocion->fecha_inicio->format('d/m/Y') }} al {{ $promocion->fecha_fin->format('d/m/Y') }}
                    @elseif($promocion->fecha_inicio)
                        Desde el {{ $promocion->fecha_inicio->format('d/m/Y') }}
                    @elseif($promocion->fecha_fin)
                        Hasta el {{ $promocion->fecha_fin->format('d/m/Y') }}
                    @else
                        ✅ Sin fecha límite - Válida indefinidamente
                    @endif
                </p>

                @if($promocion->hora_inicio && $promocion->hora_fin)
                <p><strong>🕐 Horario:</strong> De {{ \Carbon\Carbon::parse($promocion->hora_inicio)->format('h:i A') }} a {{ \Carbon\Carbon::parse($promocion->hora_fin)->format('h:i A') }}</p>
                @else
                <p><strong>🕐 Horario:</strong> ✅ Todo el día</p>
                @endif

                @if(!empty($promocion->dias_semana) && count($promocion->dias_semana) > 0)
                <p><strong>📆 Días aplicables:</strong> 
                    @php
                        $diasMap = ['lun' => 'Lunes', 'mar' => 'Martes', 'mie' => 'Miércoles', 'jue' => 'Jueves', 'vie' => 'Viernes', 'sab' => 'Sábado', 'dom' => 'Domingo'];
                        $diasNombres = array_map(function($dia) use ($diasMap) { return $diasMap[$dia] ?? $dia; }, $promocion->dias_semana);
                    @endphp
                    {{ implode(', ', $diasNombres) }}
                </p>
                @else
                <p><strong>📆 Días aplicables:</strong> ✅ Todos los días de la semana</p>
                @endif

                <p><strong>🎯 Se aplica a:</strong> 
                    @if($promocion->aplica_sobre === 'pedido')
                        Todo el pedido (descuento sobre el total)
                    @else
                        Productos específicos solamente
                    @endif
                </p>

                @if($promocion->tope_descuento)
                <p><strong>💰 Descuento máximo:</strong> ${{ number_format($promocion->tope_descuento, 2) }}</p>
                @endif
            </div>

            {{-- Requisitos --}}
            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 20px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #1e40af; margin-top: 0; font-size: 18px;">✅ Requisitos para Aplicar</h3>
                
                @if($promocion->aplica_sobre === 'item')
                    @if($promocion->productos->count() > 0)
                        <p><strong>Productos válidos:</strong></p>
                        <ul style="color: #374151; margin: 5px 0; padding-left: 20px;">
                            @foreach($promocion->productos as $producto)
                                <li>{{ $producto->nombre }}</li>
                            @endforeach
                        </ul>
                    @endif
                    
                    @if($promocion->categorias->count() > 0)
                        <p><strong>Categorías válidas:</strong></p>
                        <ul style="color: #374151; margin: 5px 0; padding-left: 20px;">
                            @foreach($promocion->categorias as $categoria)
                                <li>Todos los productos de: {{ $categoria->nombre }}</li>
                            @endforeach
                        </ul>
                    @endif
                @else
                    <p style="color: #374151;">✓ Aplica automáticamente a todos los pedidos</p>
                @endif

                @if($promocion->tipo === '2x1')
                    <p style="color: #374151; margin-top: 10px;">
                        <strong>Nota:</strong> Por cada 2 productos, solo pagas 1. El descuento se aplica automáticamente.
                    </p>
                @endif

                @if($promocion->hora_inicio && $promocion->hora_fin)
                    <p style="color: #374151; margin-top: 10px;">
                        <strong>⚠️ Importante:</strong> Solo válida en el horario especificado.
                    </p>
                @endif

                @if(!empty($promocion->dias_semana) && count($promocion->dias_semana) > 0)
                    <p style="color: #374151; margin-top: 10px;">
                        <strong>⚠️ Importante:</strong> Solo válida los días especificados.
                    </p>
                @endif
            </div>

            <p style="color: #4b5563; line-height: 1.6; background-color: #f0fdf4; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0;">
                <strong>🎉 ¡Buenas noticias!</strong> Esta promoción ya está activa en el sistema y se aplicará <strong>automáticamente</strong> a los pedidos que cumplan con las condiciones. No necesitas hacer nada, el descuento se calculará solo.
            </p>

            <center>
                <a href="{{ url('/') }}" class="cta-button">Ver Más Promociones</a>
            </center>
        </div>

        <div class="footer">
            <p>Miss Sweet Candy - Sistema de Gestión de Cafetería</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
