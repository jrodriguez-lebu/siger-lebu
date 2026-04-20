@extends('layouts.app')
@section('title', 'Reporte de Emergencias')
@section('page-title', 'Reporte de Emergencias')
@section('page-subtitle', 'Listado filtrado para análisis y exportación')

@section('content')
{{-- Filtros --}}
<div class="card mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.emergencies') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label text-xs">Desde</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input text-sm">
            </div>
            <div>
                <label class="form-label text-xs">Hasta</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input text-sm">
            </div>
            <select name="status" class="form-select text-sm">
                <option value="">Todos los estados</option>
                @foreach(['ingresada'=>'Ingresada','en_proceso'=>'En Proceso','atendida'=>'Atendida','cerrada'=>'Cerrada','cancelada'=>'Cancelada'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="type" class="form-select text-sm">
                <option value="">Todos los tipos</option>
                @foreach(['incendio'=>'Incendio','accidente_transito'=>'Tránsito','rescate'=>'Rescate','inundacion'=>'Inundación','emergencia_medica'=>'Médica','derrumbe'=>'Derrumbe','otro'=>'Otro'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('type')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="team_id" class="form-select text-sm">
                <option value="">Todos los equipos</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ request('team_id')==$team->id?'selected':'' }}>{{ $team->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-sm">Filtrar</button>
            @if(request()->hasAny(['status','type','team_id','date_from','date_to']))
                <a href="{{ route('reports.emergencies') }}" class="btn-secondary text-sm">Limpiar</a>
            @endif
            <div class="ml-auto flex gap-2">
                <a href="{{ route('reports.exportPdf', request()->all()) }}" class="btn-danger text-sm">📄 PDF</a>
                <a href="{{ route('reports.exportExcel', request()->all()) }}" class="btn-success text-sm">📊 Excel</a>
            </div>
        </form>
    </div>
</div>

{{-- Resultados --}}
<div class="card">
    <div class="card-header">
        <h3 class="font-semibold text-gray-900">Resultados: {{ $emergencies->total() }} emergencias</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Dirección</th>
                    <th>Equipo</th>
                    <th>Afectados</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($emergencies as $e)
                <tr>
                    <td><a href="{{ route('emergencies.show', $e) }}" class="font-mono text-xs text-blue-600 hover:underline">{{ $e->folio }}</a></td>
                    <td><span class="text-sm">{{ $e->getTypeIcon() }} {{ $e->getTypeLabel() }}</span></td>
                    <td><span class="badge badge-{{ $e->getPriorityColor() === 'orange' ? 'yellow' : $e->getPriorityColor() }}">{{ $e->getPriorityLabel() }}</span></td>
                    <td><span class="badge badge-{{ $e->getStatusColor() }}">{{ $e->getStatusLabel() }}</span></td>
                    <td class="max-w-xs"><p class="truncate text-sm">{{ $e->address }}</p></td>
                    <td class="text-xs text-gray-600">{{ $e->assignedTeam?->name ?? '—' }}</td>
                    <td class="text-center font-medium">{{ $e->affected_people }}</td>
                    <td class="text-xs text-gray-500">{{ $e->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-10 text-gray-400">Sin resultados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($emergencies->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $emergencies->links() }}</div>
    @endif
</div>
@endsection
