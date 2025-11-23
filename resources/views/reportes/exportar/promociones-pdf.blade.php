<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Promociones</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #ef4444; }
        .header h1 { font-size: 20px; color: #ef4444; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }
        .periodo { background-color: #f3f4f6; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .periodo strong { color: #ef4444; }
        .resumen { margin-bottom: 20px; }
        .resumen-grid { display: table; width: 100%; margin-bottom: 15px; }
        .resumen-item { display: table-cell; width: 25%; padding: 10px; background-color: #f9fafb; border: 1px solid #e5e7eb; text-align: center; }
        .resumen-item .label { font-size: 9px; color: #6b7280; margin-bottom: 5px; }
        .resumen-item .value { font-size: 16px; font-weight: bold; color: #ef4444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table thead { background-color: #ef4444; color: white; }
        table th { padding: 8px 4px; text-align: left; font-size: 9px; font-weight: bold; }
        table td { padding: 6px 4px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        table tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 7px; font-weight: bold; }
        .badge-activo { background-color: #d1fae5; color: #065f46; }
        .badge-inactivo { background-color: #f3f4f6; color: #6b7280; }
        .badge-vigente { background-color: #dbeafe; color: #1e40af; }
        .badge-vencida { background-color: #fee2e2; color: #991b1b; }
        .badge-futura { background-color: #e0e7ff; color: #3730a3; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE PROMOCIONES</h1>
        <p>Cafetería - Sistema de Gestión</p>
    </div>

    <div class="periodo">
        <strong>Tipo:</strong> {{ ucfirst($validated['tipo'] ?? 'todo') }}
        @if(!empty($validated['estado']))
            | <strong>Estado:</strong> {{ ucfirst($validated['estado']) }}
        @endif
        @if(!empty($validated['vigencia']))
            | <strong>Vigencia:</strong> {{ ucfirst($validated['vigencia']) }}
        @endif
        <br>
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <div class="resumen">
        <div class="resumen-grid">
            <div class="resumen-item">
                <div class="label">Total Promociones</div>
                <div class="value">{{ $estadisticas['total_promociones'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Activas</div>
                <div class="value" style="color: #10b981;">{{ $estadisticas['activas'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Vigentes</div>
                <div class="value" style="color: #3b82f6;">{{ $estadisticas['vigentes'] }}</div>
            </div>
            <div class="resumen-item">
                <div class="label">Vencidas</div>
                <div class="value" style="color: #ef4444;">{{ $estadisticas['vencidas'] }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 25%;">Nombre</th>
                <th style="width: 12%;">Tipo</th>
                <th class="text-right" style="width: 10%;">Valor</th>
                <th style="width: 12%;">Fecha Inicio</th>
                <th style="width: 12%;">Fecha Fin</th>
                <th class="text-center" style="width: 8%;">Activo</th>
                <th class="text-center" style="width: 8%;">Prioridad</th>
                <th class="text-center" style="width: 8%;">Vigencia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($promociones as $promo)
                <tr>
                    <td>#{{ $promo->id }}</td>
                    <td>{{ $promo->nombre }}</td>
                    <td>{{ ucfirst($promo->tipo) }}</td>
                    <td class="text-right">
                        @if($promo->tipo === 'porcentaje')
                            {{ $promo->valor }}%
                        @else
                            Bs {{ number_format($promo->valor, 2) }}
                        @endif
                    </td>
                    <td>{{ $promo->fecha_inicio ? $promo->fecha_inicio->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $promo->fecha_fin ? $promo->fecha_fin->format('d/m/Y') : 'N/A' }}</td>
                    <td class="text-center">
                        @if($promo->activo)
                            <span class="badge badge-activo">Sí</span>
                        @else
                            <span class="badge badge-inactivo">No</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $promo->prioridad }}</td>
                    <td class="text-center">
                        @if($promo->esta_vigente ?? false)
                            <span class="badge badge-vigente">Vigente</span>
                        @else
                            <span class="badge badge-vencida">No Vigente</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No hay promociones registradas</td>
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
