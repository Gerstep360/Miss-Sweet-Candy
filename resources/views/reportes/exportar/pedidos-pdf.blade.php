<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pedidos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f59e0b; }
        .header h1 { font-size: 20px; color: #f59e0b; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }
        .periodo { background-color: #f3f4f6; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .periodo strong { color: #f59e0b; }
        .resumen { margin-bottom: 20px; }
        .resumen-grid { display: table; width: 100%; margin-bottom: 15px; }
        .resumen-item { display: table-cell; width: 33.33%; padding: 10px; background-color: #f9fafb; border: 1px solid #e5e7eb; text-align: center; }
        .resumen-item .label { font-size: 9px; color: #6b7280; margin-bottom: 5px; }
        .resumen-item .value { font-size: 16px; font-weight: bold; color: #f59e0b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table thead { background-color: #f59e0b; color: white; }
        table th { padding: 8px 4px; text-align: left; font-size: 9px; font-weight: bold; }
        table td { padding: 6px 4px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        table tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 7px; font-weight: bold; }
        .badge-mesa { background-color: #dbeafe; color: #1e40af; }
        .badge-mostrador { background-color: #e9d5ff; color: #6b21a8; }
        .badge-web { background-color: #d1fae5; color: #065f46; }
        .badge-pendiente { background-color: #fee2e2; color: #991b1b; }
        .badge-pagado { background-color: #d1fae5; color: #065f46; }
        .badge-anulado { background-color: #f3f4f6; color: #6b7280; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE PEDIDOS</h1>
        <p>Cafetería - Sistema de Gestión</p>
    </div>

    <div class="periodo">
        <strong>Tipo:</strong> {{ ucfirst($validated['tipo'] ?? 'todo') }}
        @if(!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin']))
            | <strong>Período:</strong> {{ Carbon\Carbon::parse($validated['fecha_inicio'])->format('d/m/Y') }} al {{ Carbon\Carbon::parse($validated['fecha_fin'])->format('d/m/Y') }}
        @endif
        @if(!empty($validated['tipo_pedido']))
            | <strong>Tipo Pedido:</strong> {{ ucfirst($validated['tipo_pedido']) }}
        @endif
        @if(!empty($validated['estado']))
            | <strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $validated['estado'])) }}
        @endif
        <br>
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <div class="resumen">
        <div class="resumen-grid">
            <div class="resumen-item">
                <div class="label">Total Pedidos</div>
                <div class="value">{{ $estadisticas['total_pedidos'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Total Items</div>
                <div class="value">{{ $estadisticas['total_items'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Total Importe</div>
                <div class="value">Bs {{ number_format($estadisticas['total_importe'], 2) }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">ID</th>
                <th style="width: 14%;">Fecha</th>
                <th style="width: 14%;">Cliente</th>
                <th style="width: 12%;">Tipo</th>
                <th class="text-center" style="width: 8%;">Items</th>
                <th class="text-right" style="width: 14%;">Total (Bs)</th>
                <th style="width: 16%;">Estado</th>
                <th style="width: 16%;">Atendido Por</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $pedido)
                <tr>
                    <td>#{{ $pedido->id }}</td>
                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $pedido->cliente->name ?? 'N/A' }}</td>
                    <td>
                        @if($pedido->tipo === 'mesa')
                            <span class="badge badge-mesa">Mesa</span>
                        @elseif($pedido->tipo === 'mostrador')
                            <span class="badge badge-mostrador">Mostrador</span>
                        @else
                            <span class="badge badge-web">Web</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $pedido->items->sum('cantidad') }}</td>
                    <td class="text-right"><strong>{{ number_format($pedido->total, 2) }}</strong></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</td>
                    <td>{{ $pedido->atendidoPor->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay pedidos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Cafetería - Miss Sweet Candy</p>
        <p>Este documento es un reporte generado automáticamente el {{ now()->format('d/m/Y \a \l\a\s H:i:s') }}</p>
    </div>
</body>
</html>
