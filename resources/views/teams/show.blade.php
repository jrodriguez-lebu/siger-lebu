@extends('layouts.app')
@section('title', $team->name)

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if($team->color)
                <span class="inline-block w-5 h-5 rounded-full border border-gray-200" style="background-color: {{ $team->color }}"></span>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $team->name }}</h1>
                <p class="text-sm text-gray-500">{{ $team->getTypeLabel() }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('teams.edit', $team->id) }}" class="btn-secondary">Editar</a>
            <a href="{{ route('teams.index') }}" class="btn-outline">← Volver</a>
        </div>
    </div>

    {{-- Info general --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card">
            <div class="card-body">
                <p class="text-sm text-gray-500">Estado</p>
                @php
                    $sc = match($team->status) { 'activo' => 'badge-green', 'inactivo' => 'badge-yellow', default => 'badge-red' };
                    $sl = match($team->status) { 'activo' => 'Activo', 'inactivo' => 'Inactivo', default => ucfirst($team->status) };
                @endphp
                <span class="badge {{ $sc }} mt-1">{{ $sl }}</span>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="text-sm text-gray-500">Líder</p>
                <p class="font-semibold text-gray-900 mt-1">{{ $team->leader?->name ?? '—' }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="text-sm text-gray-500">Miembros</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $team->teamMembers->count() }}</p>
            </div>
        </div>
    </div>

    @if($team->description)
    <div class="card">
        <div class="card-body">
            <p class="text-sm text-gray-500 mb-1">Descripción</p>
            <p class="text-gray-700">{{ $team->description }}</p>
        </div>
    </div>
    @endif

    {{-- Miembros --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Miembros del equipo</h3>
        </div>
        <div class="card-body space-y-4">

            {{-- Agregar miembro --}}
            <form method="POST" action="{{ route('teams.addMember', $team) }}" class="flex flex-wrap gap-3 items-end p-3 bg-gray-50 rounded-lg">
                @csrf
                <div>
                    <label class="form-label">Personal</label>
                    <select name="personnel_id" class="form-select" required>
                        <option value="">— Selecciona —</option>
                        @foreach($availablePersonnel as $person)
                            <option value="{{ $person->id }}">
                                {{ $person->name }} ({{ $person->getSpecialtyLabel() }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Rol en el equipo</label>
                    <input type="text" name="role_in_team" class="form-input" placeholder="Ej: Rescatista, Médico..." required>
                </div>
                <button type="submit" class="btn-success">+ Asignar</button>
            </form>

        </div>
    </div>

    {{-- Personal del equipo --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Personal del equipo</h3>
            <a href="{{ route('personnel.create') }}?team_id={{ $team->id }}" class="btn-primary text-sm">+ Agregar Personal</a>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>RUT</th>
                        <th>Especialidad</th>
                        <th>Cargo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($team->personnel as $person)
                    <tr>
                        <td>
                            <a href="{{ route('personnel.show', $person->id) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                {{ $person->name }}
                            </a>
                        </td>
                        <td class="font-mono text-xs text-gray-500">{{ $person->rut ?? '—' }}</td>
                        <td><span class="badge {{ $person->getSpecialtyColor() }}">{{ $person->getSpecialtyLabel() }}</span></td>
                        <td class="text-sm text-gray-600">{{ $person->position ?? '—' }}</td>
                        <td class="text-sm text-gray-600">{{ $person->phone ?? '—' }}</td>
                        <td>
                            @if($person->is_active)
                                <span class="badge badge-green">Activo</span>
                            @else
                                <span class="badge badge-gray">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('personnel.edit', $person->id) }}" class="btn-secondary text-xs">Editar</a>
                                <form method="POST" action="{{ route('teams.removeMember', [$team->id, $person->id]) }}"
                                      onsubmit="return confirm('¿Desasignar a {{ addslashes($person->name) }} del equipo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-xs">Quitar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-6">
                            Sin personal registrado.
                            <a href="{{ route('personnel.create') }}?team_id={{ $team->id }}" class="text-blue-600 hover:underline ml-1">Agregar ahora</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Vehículos --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Vehículos asignados</h3>
        </div>

        {{-- Formulario asignar vehículo --}}
        <div class="card-body border-b border-gray-100">
            <form method="POST" action="{{ route('teams.addVehicle', $team->id) }}" class="flex flex-wrap gap-3 items-end p-3 bg-gray-50 rounded-lg">
                @csrf
                <div class="flex-1 min-w-48">
                    <label class="form-label">Vehículo disponible</label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">— Selecciona —</option>
                        @foreach($availableVehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->plate }}) — {{ $v->getTypeLabel() }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-success" {{ $availableVehicles->isEmpty() ? 'disabled' : '' }}>
                    + Asignar
                </button>
                @if($availableVehicles->isEmpty())
                    <p class="text-xs text-gray-400 self-center">No hay vehículos disponibles sin equipo asignado.</p>
                @endif
            </form>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Placa</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($team->vehicles as $vehicle)
                    <tr>
                        <td>
                            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="font-medium text-blue-600 hover:underline">
                                {{ $vehicle->name }}
                            </a>
                        </td>
                        <td class="font-mono text-sm">{{ $vehicle->plate }}</td>
                        <td>{{ $vehicle->getTypeLabel() }}</td>
                        <td>
                            <span class="badge badge-{{ $vehicle->getStatusColor() }}">{{ $vehicle->getStatusLabel() }}</span>
                        </td>
                        <td class="text-right">
                            <form method="POST" action="{{ route('teams.removeVehicle', [$team->id, $vehicle->id]) }}"
                                  onsubmit="return confirm('¿Desasignar {{ addslashes($vehicle->name) }} del equipo?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger text-xs">Quitar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-400 py-6">Sin vehículos asignados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Emergencias recientes --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Emergencias del equipo</h3>
            <span class="text-sm text-gray-400">(últimas 10)</span>
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
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($team->emergencies as $emergency)
                    <tr>
                        <td>
                            <a href="{{ route('emergencies.show', $emergency->id) }}" class="font-mono text-blue-600 hover:underline">
                                {{ $emergency->folio }}
                            </a>
                        </td>
                        <td>{{ $emergency->getTypeLabel() }}</td>
                        <td>
                            <span class="badge badge-{{ $emergency->getPriorityColor() }}">{{ $emergency->getPriorityLabel() }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $emergency->getStatusColor() }}">{{ $emergency->getStatusLabel() }}</span>
                        </td>
                        <td class="text-sm text-gray-600 truncate max-w-xs">{{ $emergency->address }}</td>
                        <td class="text-sm text-gray-500">{{ $emergency->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-6">Sin emergencias registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
