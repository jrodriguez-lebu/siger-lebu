@extends('layouts.app')
@section('title', 'Vehículos')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vehículos</h1>
            <p class="text-sm text-gray-500">Flota vehicular del sistema</p>
        </div>
        <a href="{{ route('vehicles.create') }}" class="btn-primary">+ Nuevo Vehículo</a>
    </div>

    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('vehicles.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nombre, placa, marca...">
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponible"        {{ request('status') === 'disponible'        ? 'selected' : '' }}>Disponible</option>
                        <option value="en_servicio"       {{ request('status') === 'en_servicio'       ? 'selected' : '' }}>En Servicio</option>
                        <option value="mantenimiento"     {{ request('status') === 'mantenimiento'     ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="fuera_de_servicio" {{ request('status') === 'fuera_de_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select">
                        <option value="">Todos</option>
                        <option value="ambulancia"      {{ request('type') === 'ambulancia'      ? 'selected' : '' }}>Ambulancia</option>
                        <option value="camion_bomberos" {{ request('type') === 'camion_bomberos' ? 'selected' : '' }}>Camión de Bomberos</option>
                        <option value="camioneta"       {{ request('type') === 'camioneta'       ? 'selected' : '' }}>Camioneta</option>
                        <option value="furgon"          {{ request('type') === 'furgon'          ? 'selected' : '' }}>Furgón</option>
                        <option value="moto"            {{ request('type') === 'moto'            ? 'selected' : '' }}>Motocicleta</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Equipo</label>
                    <select name="team_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Filtrar</button>
                <a href="{{ route('vehicles.index') }}" class="btn-secondary">Limpiar</a>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Vehículo</th>
                        <th>Placa</th>
                        <th>Tipo</th>
                        <th>Equipo</th>
                        <th>Estado</th>
                        <th>Alertas</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                    <tr>
                        <td>
                            <p class="font-medium text-gray-900">{{ $vehicle->name }}</p>
                            <p class="text-xs text-gray-400">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }}</p>
                        </td>
                        <td class="font-mono text-sm font-semibold">{{ $vehicle->plate }}</td>
                        <td>{{ $vehicle->getTypeLabel() }}</td>
                        <td>{{ $vehicle->team?->name ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $vehicle->getStatusColor() }}">{{ $vehicle->getStatusLabel() }}</span>
                        </td>
                        <td>
                            @if($vehicle->isInsuranceExpiringSoon())
                                <span class="badge badge-red text-xs">Seguro vence</span>
                            @endif
                            @if($vehicle->isTechnicalReviewExpiringSoon())
                                <span class="badge badge-yellow text-xs">Rev. técnica</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn-outline text-sm">Ver</a>
                                <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn-secondary text-sm">Editar</a>
                                <form method="POST" action="{{ route('vehicles.destroy', $vehicle->id) }}"
                                      onsubmit="return confirm('¿Eliminar el vehículo {{ addslashes($vehicle->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-10">No se encontraron vehículos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vehicles->hasPages())
        <div class="card-body border-t border-gray-100">
            {{ $vehicles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
