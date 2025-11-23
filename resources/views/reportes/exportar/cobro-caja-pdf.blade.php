<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cobros de Caja</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #10b981; }
        .header h1 { font-size: 20px; color: #10b981; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }
        .periodo { background-color: #f3f4f6; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .periodo strong { color: #10b981; }
        .resumen { margin-bottom: 20px; }
        .resumen-grid { display: table; width: 100%; margin-bottom: 15px; }
        .resumen-item { display: table-cell; width: 25%; padding: 10px; background-color: #f9fafb; border: 1px solid #e5e7eb; text-align: center; }
        .resumen-item .label { font-size: 9px; color: #6b7280; margin-bottom: 5px; }
        .resumen-item .value { font-size: 16px; font-weight: bold; color: #10b981; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table thead { background-color: #10b981; color: white; }
        table th { padding: 8px 5px; text-align: left; font-size: 9px; font-weight: bold; }
        table td { padding: 6px 5px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        table tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-efectivo { background-color: #d1fae5; color: #065f46; }
        .badge-pos { background-color: #dbeafe; color: #1e40af; }
        .badge-qr { background-color: #e9d5ff; color: #6b21a8; }
        .badge-cobrado { background-color: #d1fae5; color: #065f46; }
        .badge-cancelado { background-color: #fee2e2; color: #991b1b; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE COBROS DE CAJA</h1>
        <p>Cafetería - Sistema de Gestión</p>
    </div>

    <div class="periodo">
        <strong>Tipo:</strong> {{ ucfirst($validated['tipo'] ?? 'todo') }}
        @if(!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin']))
            | <strong>Período:</strong> {{ Carbon\Carbon::parse($validated['fecha_inicio'])->format('d/m/Y') }} al {{ Carbon\Carbon::parse($validated['fecha_fin'])->format('d/m/Y') }}
        @endif
        @if(!empty($validated['metodo_pago']))
            | <strong>Método:</strong> {{ ucfirst($validated['metodo_pago']) }}
        @endif
        <br>
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <div class="resumen">
        <div class="resumen-grid">
            <div class="resumen-item">
                <div class="label">Total Cobros</div>
                <div class="value">{{ $estadisticas['total_cobros'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Total Importe</div>
                <div class="value">Bs {{ number_format($estadisticas['total_importe'], 2) }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Cobrados</div>
                <div class="value" style="color: #10b981;">{{ $estadisticas['cobrados'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Cancelados</div>
                <div class="value" style="color: #ef4444;">{{ $estadisticas['cancelados'] }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">ID</th>
                <th style="width: 12%;">Fecha</th>
                <th style="width: 10%;">Hora</th>
                <th style="width: 10%;">Pedido</th>
                <th style="width: 15%;">Método</th>
                <th style="width: 22%;">Cajero</th>
                <th class="text-right" style="width: 15%;">Importe (Bs)</th>
                <th class="text-center" style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cobros as $cobro)
                <tr>
                    <td>#{{ $cobro->id }}</td>
                    <td>{{ $cobro->created_at->format('d/m/Y') }}</td>
                    <td>{{ $cobro->created_at->format('H:i:s') }}</td>
                    <td>#{{ $cobro->pedido_id }}</td>
                    <td>
                        @if($cobro->metodo === 'efectivo')
                            <span class="badge badge-efectivo">Efectivo</span>
                        @elseif($cobro->metodo === 'pos')
                            <span class="badge badge-pos">POS</span>
                        @else
                            <span class="badge badge-qr">QR</span>
                        @endif
                    </td>
                    <td>{{ $cobro->cajero->name ?? 'N/A' }}</td>
                    <td class="text-right"><strong>{{ number_format($cobro->importe, 2) }}</strong></td>
                    <td class="text-center">
                        @if($cobro->estado === 'cobrado')
                            <span class="badge badge-cobrado">Cobrado</span>
                        @else
                            <span class="badge badge-cancelado">Cancelado</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay cobros registrados</td>
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
