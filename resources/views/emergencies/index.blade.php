@extends('layouts.app')
@section('title', 'Emergencias')
@section('page-title', 'Gestión de Emergencias')
@section('page-subtitle', 'Registro y seguimiento de todas las emergencias')

@section('content')

{{-- Filtros --}}
<div class="card mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('emergencies.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar folio, dirección, nombre..."
                   class="form-input w-full sm:w-64">

            <select name="status" class="form-select w-auto">
                <option value="">Todos los estados</option>
                @foreach(['ingresada' => 'Ingresada','en_proceso' => 'En Proceso','atendida' => 'Atendida','cerrada' => 'Cerrada','cancelada' => 'Cancelada'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="priority" class="form-select w-auto">
                <option value="">Todas las prioridades</option>
                @foreach(['critica' => 'Crítica','alta' => 'Alta','media' => 'Media','baja' => 'Baja'] as $val => $label)
                    <option value="{{ $val }}" {{ request('priority') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="type" class="form-select w-auto">
                <option value="">Todos los tipos</option>
                @foreach(['incendio' => '🔥 Incendio','accidente_transito' => '🚗 Tránsito','rescate' => '🆘 Rescate','inundacion' => '🌊 Inundación','emergencia_medica' => '🏥 Médica','derrumbe' => '⛰️ Derrumbe','otro' => '⚠️ Otro'] as $val => $label)
                    <option value="{{ $val }}" {{ request('type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-auto" title="Desde">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input w-auto" title="Hasta">

            <button type="submit" class="btn-primary">Filtrar</button>
            @if(request()->hasAny(['search','status','type','priority','date_from','date_to']))
                <a href="{{ route('emergencies.index') }}" class="btn-secondary">Limpiar</a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'coordinador', 'digitador']))
                <a href="{{ route('emergencies.create') }}" class="btn-danger ml-auto">+ Nueva</a>
            @endif
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Tipo</th>
                    <th>Dirección</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Equipo</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($emergencies as $emergency)
                <tr>
                    <td>
                        <span class="font-mono text-xs font-medium text-gray-900">{{ $emergency->folio }}</span>
                        @if($emergency->photos->count())
                        <span class="ml-1 text-xs text-gray-400">📷{{ $emergency->photos->count() }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-base">{{ $emergency->getTypeIcon() }}</span>
                        <span class="text-xs text-gray-600 ml-1">{{ $emergency->getTypeLabel() }}</span>
                    </td>
                    <td>
                        <p class="text-sm text-gray-900 max-w-xs truncate">{{ $emergency->address }}</p>
                        @if($emergency->sector)
                        <p class="text-xs text-gray-400">{{ $emergency->sector }}</p>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $emergency->getPriorityColor() === 'orange' ? 'yellow' : $emergency->getPriorityColor() }}">
                            {{ $emergency->getPriorityLabel() }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $emergency->getStatusColor() }}">
                            {{ $emergency->getStatusLabel() }}
                        </span>
                    </td>
                    <td>
                        @if($emergency->assignedTeam)
                        <span class="text-xs text-blue-700 font-medium">{{ $emergency->assignedTeam->name }}</span>
                        @else
                        <span class="text-xs text-gray-400">Sin asignar</span>
                        @endif
                    </td>
                    <td>
                        <p class="text-xs text-gray-500">{{ $emergency->created_at->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $emergency->created_at->format('H:i') }}</p>
                    </td>
                    <td>
                        <a href="{{ route('emergencies.show', $emergency->id) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <p class="text-3xl mb-2">🔍</p>
                        <p class="text-sm">No se encontraron emergencias con los filtros seleccionados</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($emergencies->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $emergencies->links() }}
    </div>
    @endif
</div>

@endsection
