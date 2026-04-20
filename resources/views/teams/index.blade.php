@extends('layouts.app')
@section('title', 'Equipos')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Equipos</h1>
            <p class="text-sm text-gray-500">Gestión de equipos de respuesta</p>
        </div>
        <a href="{{ route('teams.create') }}" class="btn-primary">+ Nuevo Equipo</a>
    </div>

    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('teams.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="form-label">Buscar nombre</label>
                    <input type="text" name="name" value="{{ request('name') }}" class="form-input" placeholder="Nombre del equipo">
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('status') === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('status') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        <option value="disuelto" {{ request('status') === 'disuelto' ? 'selected' : '' }}>Disuelto</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select">
                        <option value="">Todos</option>
                        <option value="rescate" {{ request('type') === 'rescate' ? 'selected' : '' }}>Rescate</option>
                        <option value="tecnico" {{ request('type') === 'tecnico' ? 'selected' : '' }}>Técnico</option>
                        <option value="bombero" {{ request('type') === 'bombero' ? 'selected' : '' }}>Bomberos</option>
                        <option value="medico" {{ request('type') === 'medico' ? 'selected' : '' }}>Médico</option>
                        <option value="apoyo" {{ request('type') === 'apoyo' ? 'selected' : '' }}>Apoyo</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Filtrar</button>
                <a href="{{ route('teams.index') }}" class="btn-secondary">Limpiar</a>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Tipo</th>
                        <th>Líder</th>
                        <th>Miembros</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teams as $team)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                @if($team->color)
                                    <span class="inline-block w-3 h-3 rounded-full" style="background-color: {{ $team->color }}"></span>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $team->name }}</p>
                                    @if($team->description)
                                        <p class="text-xs text-gray-500 truncate max-w-xs">{{ $team->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-blue">{{ $team->getTypeLabel() }}</span>
                        </td>
                        <td>
                            {{ $team->leader?->name ?? '—' }}
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $team->team_members_count }} miembro(s)</span>
                        </td>
                        <td>
                            @php
                                $statusColor = match($team->status) {
                                    'activo'   => 'badge-green',
                                    'inactivo' => 'badge-yellow',
                                    'disuelto' => 'badge-red',
                                    default    => 'badge-gray',
                                };
                                $statusLabel = match($team->status) {
                                    'activo'   => 'Activo',
                                    'inactivo' => 'Inactivo',
                                    'disuelto' => 'Disuelto',
                                    default    => ucfirst($team->status),
                                };
                            @endphp
                            <span class="badge {{ $statusColor }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('teams.show', $team->id) }}" class="btn-outline text-sm">Ver</a>
                                <a href="{{ route('teams.edit', $team->id) }}" class="btn-secondary text-sm">Editar</a>
                                <form method="POST" action="{{ route('teams.destroy', $team->id) }}"
                                      onsubmit="return confirm('¿Eliminar el equipo {{ addslashes($team->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-10">No se encontraron equipos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($teams->hasPages())
        <div class="card-body border-t border-gray-100">
            {{ $teams->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
