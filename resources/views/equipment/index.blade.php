@extends('layouts.app')
@section('title', 'Equipamiento')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Equipamiento</h1>
            <p class="text-sm text-gray-500">Inventario de equipos y herramientas</p>
        </div>
        <a href="{{ route('equipment.create') }}" class="btn-primary">+ Nuevo Equipo</a>
    </div>

    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('equipment.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nombre, código...">
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponible"   {{ request('status') === 'disponible'   ? 'selected' : '' }}>Disponible</option>
                        <option value="en_uso"       {{ request('status') === 'en_uso'       ? 'selected' : '' }}>En Uso</option>
                        <option value="mantenimiento"{{ request('status') === 'mantenimiento'? 'selected' : '' }}>Mantenimiento</option>
                        <option value="dado_de_baja" {{ request('status') === 'dado_de_baja' ? 'selected' : '' }}>Dado de Baja</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Categoría</label>
                    <select name="category" class="form-select">
                        <option value="">Todas</option>
                        <option value="herramienta"    {{ request('category') === 'herramienta'    ? 'selected' : '' }}>Herramienta</option>
                        <option value="equipo_medico"  {{ request('category') === 'equipo_medico'  ? 'selected' : '' }}>Equipo Médico</option>
                        <option value="equipo_rescate" {{ request('category') === 'equipo_rescate' ? 'selected' : '' }}>Equipo de Rescate</option>
                        <option value="comunicacion"   {{ request('category') === 'comunicacion'   ? 'selected' : '' }}>Comunicación</option>
                        <option value="proteccion"     {{ request('category') === 'proteccion'     ? 'selected' : '' }}>Protección</option>
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
                <a href="{{ route('equipment.index') }}" class="btn-secondary">Limpiar</a>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th>Equipo</th>
                        <th>Estado</th>
                        <th>Mantenimiento</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipment as $item)
                    <tr class="{{ $item->needsMaintenanceSoon() ? 'bg-amber-50' : '' }}">
                        <td>
                            <p class="font-medium text-gray-900">{{ $item->name }}</p>
                            @if($item->brand || $item->model)
                                <p class="text-xs text-gray-400">{{ $item->brand }} {{ $item->model }}</p>
                            @endif
                        </td>
                        <td class="font-mono text-sm">{{ $item->code ?? '—' }}</td>
                        <td>
                            <span class="badge badge-blue">{{ $item->getCategoryLabel() }}</span>
                        </td>
                        <td>{{ $item->team?->name ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $item->getStatusColor() }}">{{ $item->getStatusLabel() }}</span>
                        </td>
                        <td>
                            @if($item->needsMaintenanceSoon())
                                <span class="badge badge-red text-xs">
                                    Prox. mant.: {{ $item->next_maintenance?->format('d/m/Y') }}
                                </span>
                            @elseif($item->next_maintenance)
                                <span class="text-xs text-gray-500">{{ $item->next_maintenance->format('d/m/Y') }}</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('equipment.edit', $item->id) }}" class="btn-secondary text-sm">Editar</a>
                                <form method="POST" action="{{ route('equipment.destroy', $item->id) }}"
                                      onsubmit="return confirm('¿Eliminar {{ addslashes($item->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-10">No se encontró equipamiento.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($equipment->hasPages())
        <div class="card-body border-t border-gray-100">
            {{ $equipment->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
