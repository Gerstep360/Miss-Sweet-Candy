<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Arqueos / Cierres de Caja</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #3b82f6; }
        .header h1 { font-size: 20px; color: #3b82f6; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }
        .periodo { background-color: #f3f4f6; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .periodo strong { color: #3b82f6; }
        .resumen { margin-bottom: 20px; }
        .resumen-grid { display: table; width: 100%; margin-bottom: 15px; }
        .resumen-item { display: table-cell; padding: 10px; background-color: #f9fafb; border: 1px solid #e5e7eb; text-align: center; }
        .resumen-item .label { font-size: 9px; color: #6b7280; margin-bottom: 5px; }
        .resumen-item .value { font-size: 16px; font-weight: bold; color: #3b82f6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9px; }
        table thead { background-color: #3b82f6; color: white; }
        table th { padding: 8px 4px; text-align: left; font-size: 8px; font-weight: bold; }
        table td { padding: 6px 4px; border-bottom: 1px solid #e5e7eb; font-size: 8px; }
        table tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 7px; font-weight: bold; }
        .badge-cuadrado { background-color: #d1fae5; color: #065f46; }
        .badge-diferencia { background-color: #fed7aa; color: #92400e; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE ARQUEOS / CIERRES DE CAJA</h1>
        <p>Cafetería - Sistema de Gestión</p>
    </div>

    <div class="periodo">
        <strong>Tipo:</strong> {{ ucfirst($validated['tipo'] ?? 'todo') }}
        @if(!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin']))
            | <strong>Período:</strong> {{ Carbon\Carbon::parse($validated['fecha_inicio'])->format('d/m/Y') }} al {{ Carbon\Carbon::parse($validated['fecha_fin'])->format('d/m/Y') }}
        @endif
        @if(!empty($validated['con_diferencias']))
            | <strong>Filtro:</strong> Solo con diferencias
        @endif
        <br>
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <div class="resumen">
        <div class="resumen-grid">
            <div class="resumen-item" style="width: 20%;">
                <div class="label">Total Arqueos</div>
                <div class="value">{{ $estadisticas['total_arqueos'] }}</div>
            </div>
            <div class="resumen-item" style="width: 20%;">
                <div class="label">Total Sistema</div>
                <div class="value">Bs {{ number_format($estadisticas['total_sistema'], 2) }}</div>
            </div>
            <div class="resumen-item" style="width: 20%;">
                <div class="label">Total Declarado</div>
                <div class="value">Bs {{ number_format($estadisticas['total_declarado'], 2) }}</div>
            </div>
            <div class="resumen-item" style="width: 20%;">
                <div class="label">Cuadrados</div>
                <div class="value" style="color: #10b981;">{{ $estadisticas['cuadrados'] }}</div>
            </div>
            <div class="resumen-item" style="width: 20%;">
                <div class="label">Con Diferencias</div>
                <div class="value" style="color: #f59e0b;">{{ $estadisticas['con_diferencias'] }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 18%;">Cajero</th>
                <th style="width: 13%;">Inicio</th>
                <th style="width: 13%;">Fin</th>
                <th class="text-right" style="width: 12%;">Sistema (Bs)</th>
                <th class="text-right" style="width: 12%;">Declarado (Bs)</th>
                <th class="text-right" style="width: 12%;">Diferencia (Bs)</th>
                <th class="text-center" style="width: 15%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($arqueos as $arqueo)
                <tr>
                    <td>#{{ $arqueo->id }}</td>
                    <td>{{ $arqueo->cajero->name ?? 'N/A' }}</td>
                    <td>{{ $arqueo->inicio->format('d/m/Y H:i') }}</td>
                    <td>{{ $arqueo->fin->format('d/m/Y H:i') }}</td>
                    <td class="text-right">{{ number_format($arqueo->total_sistema, 2) }}</td>
                    <td class="text-right">{{ number_format($arqueo->total_declarado, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($arqueo->diferencia, 2) }}</strong></td>
                    <td class="text-center">
                        @if($arqueo->diferencia == 0)
                            <span class="badge badge-cuadrado">Cuadrado</span>
                        @else
                            <span class="badge badge-diferencia">Diferencia</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay cierres de caja registrados</td>
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
