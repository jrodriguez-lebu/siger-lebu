@extends('layouts.app')
@section('title', 'Personal')
@section('page-title', 'Personal')
@section('page-subtitle', 'Registro del personal operativo de todos los equipos')

@section('content')
<div class="space-y-4">

    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-40">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nombre, RUT, cargo...">
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
                <div>
                    <label class="form-label">Especialidad</label>
                    <select name="specialty" class="form-select">
                        <option value="">Todas</option>
                        @foreach(['bombero'=>'Bombero','paramedico'=>'Paramédico','enfermero'=>'Enfermero/a','medico'=>'Médico','rescatista'=>'Rescatista','logistica'=>'Logística','comunicaciones'=>'Comunicaciones','carabinero'=>'Carabinero','voluntario'=>'Voluntario','otro'=>'Otro'] as $val => $label)
                            <option value="{{ $val }}" {{ request('specialty') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary">Filtrar</button>
                    @if(request()->hasAny(['search','team_id','specialty','status']))
                        <a href="{{ route('personnel.index') }}" class="btn-outline">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">
                {{ $personnel->total() }} persona{{ $personnel->total() != 1 ? 's' : '' }} registrada{{ $personnel->total() != 1 ? 's' : '' }}
            </h3>
            @if(in_array(auth()->user()->role, ['admin','coordinador','lider']))
            <a href="{{ route('personnel.create') }}" class="btn-primary">+ Agregar Personal</a>
            @endif
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>RUT</th>
                        <th>Especialidad</th>
                        <th>Cargo / Función</th>
                        <th>Equipo</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($personnel as $person)
                    <tr>
                        <td>
                            <a href="{{ route('personnel.show', $person->id) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                {{ $person->name }}
                            </a>
                        </td>
                        <td class="font-mono text-sm text-gray-600">{{ $person->rut ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $person->getSpecialtyColor() }}">{{ $person->getSpecialtyLabel() }}</span>
                        </td>
                        <td class="text-sm text-gray-600">{{ $person->position ?? '—' }}</td>
                        <td class="text-sm text-gray-700">{{ $person->team?->name ?? '—' }}</td>
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
                                <form method="POST" action="{{ route('personnel.destroy', $person->id) }}"
                                      onsubmit="return confirm('¿Eliminar a {{ addslashes($person->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-xs">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12">
                            <p class="text-3xl mb-2">👥</p>
                            <p class="text-gray-400 font-medium">No hay personal registrado</p>
                            <a href="{{ route('personnel.create') }}" class="text-blue-600 text-sm mt-1 inline-block hover:underline">Agregar el primero</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($personnel->hasPages())
        <div class="px-6 py-3 border-t border-gray-100">
            {{ $personnel->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
