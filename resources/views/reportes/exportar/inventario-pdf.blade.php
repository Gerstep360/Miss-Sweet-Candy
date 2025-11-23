<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario</title>
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
            border-bottom: 2px solid #9333ea;
        }
        
        .header h1 {
            font-size: 20px;
            color: #9333ea;
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
            color: #9333ea;
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
        }
        
        .value-ok { color: #10b981; }
        .value-bajo { color: #f59e0b; }
        .value-critico { color: #ef4444; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table thead {
            background-color: #9333ea;
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
        
        .badge-ok {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-bajo {
            background-color: #fed7aa;
            color: #92400e;
        }
        
        .badge-critico {
            background-color: #fecaca;
            color: #991b1b;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>REPORTE DE INVENTARIO</h1>
        <p>Cafetería - Sistema de Gestión</p>
    </div>

    {{-- Información --}}
    <div class="periodo">
        <strong>Tipo de Reporte:</strong> {{ ucfirst($validated['tipo'] ?? 'todo') }}
        @if(!empty($validated['estado_stock']))
            | <strong>Estado:</strong> {{ $validated['estado_stock'] }}
        @endif
        @if(!empty($validated['categoria_id']))
            | <strong>Categoría filtrada</strong>
        @endif
        <br>
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
    </div>

    {{-- Resumen --}}
    <div class="resumen">
        <div class="resumen-grid">
            <div class="resumen-item">
                <div class="label">Total Productos</div>
                <div class="value" style="color: #9333ea;">{{ $estadisticas['total_productos'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Stock OK</div>
                <div class="value value-ok">{{ $estadisticas['ok'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Stock Bajo</div>
                <div class="value value-bajo">{{ $estadisticas['bajos'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Stock Crítico</div>
                <div class="value value-critico">{{ $estadisticas['criticos'] }}</div>
            </div>
        </div>
    </div>

    {{-- Detalle --}}
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 30%;">Producto</th>
                <th style="width: 20%;">Categoría</th>
                <th class="text-center" style="width: 10%;">Stock Actual</th>
                <th class="text-center" style="width: 10%;">Stock Mínimo</th>
                <th class="text-center" style="width: 12%;">P. Repos.</th>
                <th class="text-center" style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventarios as $inv)
                <tr>
                    <td>#{{ $inv->id }}</td>
                    <td>{{ $inv->producto->nombre ?? 'N/A' }}</td>
                    <td>{{ $inv->producto->categoria->nombre ?? 'Sin categoría' }}</td>
                    <td class="text-center"><strong>{{ $inv->stock_actual }}</strong></td>
                    <td class="text-center">{{ $inv->stock_minimo }}</td>
                    <td class="text-center">{{ $inv->punto_reposicion }}</td>
                    <td class="text-center">
                        @if($inv->estado_stock === 'OK')
                            <span class="badge badge-ok">OK</span>
                        @elseif($inv->estado_stock === 'BAJO')
                            <span class="badge badge-bajo">BAJO</span>
                        @else
                            <span class="badge badge-critico">CRÍTICO</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay productos en el inventario</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Cafetería - Miss Sweet Candy</p>
        <p>Este documento es un reporte generado automáticamente el {{ now()->format('d/m/Y \a \l\a\s H:i:s') }}</p>
    </div>
</body>
</html>
