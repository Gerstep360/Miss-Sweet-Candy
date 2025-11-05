<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f59e0b;
        }
        
        .header h1 {
            font-size: 20px;
            color: #f59e0b;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .periodo {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .periodo strong {
            color: #f59e0b;
        }
        
        .resumen {
            margin-bottom: 20px;
        }
        
        .resumen-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .resumen-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        
        .resumen-item .label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .resumen-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #10b981;
        }
        
        .seccion {
            margin-bottom: 20px;
        }
        
        .seccion h3 {
            font-size: 13px;
            color: #374151;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table thead {
            background-color: #f59e0b;
            color: white;
        }
        
        table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        
        table td {
            padding: 6px 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-mesa {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-mostrador {
            background-color: #e9d5ff;
            color: #6b21a8;
        }
        
        .badge-web {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-efectivo {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-pos {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-qr {
            background-color: #e9d5ff;
            color: #6b21a8;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
        
        .totales {
            background-color: #fef3c7;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }
        
        .totales table {
            margin-bottom: 0;
        }
        
        .totales td {
            border: none;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>REPORTE DE VENTAS</h1>
        <p>Cafetería - Sistema de Gestión</p>
    </div>

    {{-- Período --}}
    <div class="periodo">
        <strong>Período:</strong> {{ Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        @if($tipoVenta !== 'todos')
            | <strong>Tipo:</strong> {{ ucfirst($tipoVenta) }}
        @endif
        @if($metodoPago !== 'todos')
            | <strong>Método:</strong> {{ ucfirst($metodoPago) }}
        @endif
        <br>
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
    </div>

    {{-- Resumen --}}
    <div class="resumen">
        <h3>Resumen General</h3>
        <div class="resumen-grid">
            <div class="resumen-item">
                <div class="label">Total Ventas</div>
                <div class="value">Bs {{ number_format($resumen['total_ventas'], 2) }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Cantidad</div>
                <div class="value" style="color: #3b82f6;">{{ $resumen['cantidad_ventas'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Promedio</div>
                <div class="value" style="color: #f59e0b;">Bs {{ number_format($resumen['promedio_venta'], 2) }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Items Vendidos</div>
                <div class="value" style="color: #8b5cf6;">{{ $resumen['total_items'] }}</div>
            </div>
        </div>
    </div>

    {{-- Ventas por Tipo --}}
    <div class="seccion">
        <h3>Ventas por Tipo</h3>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Total (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventasPorTipo as $tipo)
                    <tr>
                        <td>
                            @if($tipo->tipo === 'mesa')
                                <span class="badge badge-mesa">Mesa</span>
                            @elseif($tipo->tipo === 'mostrador')
                                <span class="badge badge-mostrador">Mostrador</span>
                            @else
                                <span class="badge badge-web">En Linea</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $tipo->cantidad }}</td>
                        <td class="text-right"><strong>{{ number_format($tipo->total, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Sin datos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Ventas por Método --}}
    <div class="seccion">
        <h3>Ventas por Método de Pago</h3>
        <table>
            <thead>
                <tr>
                    <th>Método</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Total (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventasPorMetodo as $metodo)
                    <tr>
                        <td>
                            @if($metodo->metodo === 'efectivo')
                                <span class="badge badge-efectivo">Efectivo</span>
                            @elseif($metodo->metodo === 'pos')
                                <span class="badge badge-pos">POS</span>
                            @else
                                <span class="badge badge-qr">QR</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $metodo->cantidad }}</td>
                        <td class="text-right"><strong>{{ number_format($metodo->total, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Sin datos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Detalle de Ventas --}}
    <div class="seccion">
        <h3>Detalle de Ventas ({{ $ventas->count() }} registros)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th style="width: 15%;">Fecha/Hora</th>
                    <th style="width: 10%;">Pedido</th>
                    <th style="width: 15%;">Tipo</th>
                    <th style="width: 15%;">Método</th>
                    <th style="width: 20%;">Cajero</th>
                    <th class="text-right" style="width: 17%;">Importe (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                    <tr>
                        <td>#{{ $venta->id }}</td>
                        <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                        <td>#{{ $venta->pedido_id }}</td>
                        <td>
                            @if($venta->pedido->tipo === 'mesa')
                                <span class="badge badge-mesa">Mesa</span>
                            @elseif($venta->pedido->tipo === 'mostrador')
                                <span class="badge badge-mostrador">Mostrador</span>
                            @else
                                <span class="badge badge-web">Web</span>
                            @endif
                        </td>
                        <td>
                            @if($venta->metodo === 'efectivo')
                                <span class="badge badge-efectivo">Efectivo</span>
                            @elseif($venta->metodo === 'pos')
                                <span class="badge badge-pos">POS</span>
                            @else
                                <span class="badge badge-qr">QR</span>
                            @endif
                        </td>
                        <td>{{ $venta->cajero->name ?? 'N/A' }}</td>
                        <td class="text-right"><strong>{{ number_format($venta->importe, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay ventas en este período</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($ventas->count() > 0)
            <div class="totales">
                <table>
                    <tr>
                        <td style="width: 70%;" class="text-right">TOTAL GENERAL:</td>
                        <td style="width: 30%;" class="text-right" style="font-size: 14px; color: #10b981;">
                            Bs {{ number_format($resumen['total_ventas'], 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Cafetería - Miss Sweet Candy</p>
        <p>Este documento es un reporte generado automáticamente el {{ now()->format('d/m/Y \a \l\a\s H:i:s') }}</p>
    </div>
</body>
</html>
