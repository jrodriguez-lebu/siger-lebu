<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Emergencias — SIGER</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; margin: 20px; }
    h1 { font-size: 16px; color: #1e3a8a; margin-bottom: 4px; }
    .subtitle { font-size: 10px; color: #6b7280; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #1e3a8a; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
    td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
    tr:nth-child(even) { background: #f8fafc; }
    .badge { display: inline-block; padding: 2px 6px; border-radius: 999px; font-size: 8px; font-weight: bold; }
    .badge-red    { background: #fee2e2; color: #991b1b; }
    .badge-yellow { background: #fef9c3; color: #854d0e; }
    .badge-green  { background: #dcfce7; color: #166534; }
    .badge-blue   { background: #dbeafe; color: #1e40af; }
    .badge-gray   { background: #f3f4f6; color: #374151; }
    .footer { margin-top: 20px; font-size: 8px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
    <h1>🚨 SIGER — Reporte de Emergencias</h1>
    <p class="subtitle">
        Municipalidad de Lebu · Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()?->name ?? 'Sistema' }}
        @if(!empty(array_filter($filters))) · Filtros aplicados @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Tipo</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Dirección</th>
                <th>Equipo</th>
                <th>Afect.</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($emergencies as $e)
            <tr>
                <td><strong>{{ $e->folio }}</strong></td>
                <td>{{ $e->getTypeLabel() }}</td>
                <td>
                    <span class="badge badge-{{ $e->getPriorityColor() === 'orange' ? 'yellow' : $e->getPriorityColor() }}">
                        {{ $e->getPriorityLabel() }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $e->getStatusColor() }}">{{ $e->getStatusLabel() }}</span>
                </td>
                <td>{{ $e->address }}</td>
                <td>{{ $e->assignedTeam?->name ?? '—' }}</td>
                <td>{{ $e->affected_people }}</td>
                <td>{{ $e->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:16px">Sin emergencias</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        SIGER · Unidad de Gestión de Riesgo de Desastres · Municipalidad de Lebu · Total: {{ $emergencies->count() }} registros
    </p>
</body>
</html>
