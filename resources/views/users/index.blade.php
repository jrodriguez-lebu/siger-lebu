@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
            <p class="text-sm text-gray-500">Gestión de cuentas del sistema</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">+ Nuevo Usuario</a>
    </div>

    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nombre, email, teléfono...">
                </div>
                <div>
                    <label class="form-label">Rol</label>
                    <select name="role" class="form-select">
                        <option value="">Todos los roles</option>
                        <option value="admin"       {{ request('role') === 'admin'       ? 'selected' : '' }}>Super Admin</option>
                        <option value="coordinador" {{ request('role') === 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                        <option value="lider"       {{ request('role') === 'lider'       ? 'selected' : '' }}>Líder</option>
                        <option value="digitador"   {{ request('role') === 'digitador'   ? 'selected' : '' }}>Digitador</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="active" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Filtrar</button>
                <a href="{{ route('users.index') }}" class="btn-secondary">Limpiar</a>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="{{ $user->trashed() ? 'opacity-60 bg-gray-50' : '' }}">
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                     class="w-8 h-8 rounded-full object-cover">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    @if($user->trashed())
                                        <span class="text-xs text-red-500">Eliminado</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="text-sm text-gray-600">{{ $user->phone ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $user->getRoleBadgeColor() }}">{{ $user->getRoleLabel() }}</span>
                        </td>
                        <td>
                            @if(!$user->trashed())
                            <form method="POST" action="{{ route('users.toggleActive', $user) }}">
                                @csrf
                                <button type="submit"
                                        class="badge {{ $user->active ? 'badge-green' : 'badge-red' }} cursor-pointer hover:opacity-80 transition-opacity border-0"
                                        title="Clic para {{ $user->active ? 'desactivar' : 'activar' }}">
                                    {{ $user->active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                            @else
                                <span class="badge badge-gray">Eliminado</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(!$user->trashed())
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-secondary text-sm">Editar</a>
                                <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                      onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-sm">Eliminar</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-10">No se encontraron usuarios.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="card-body border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
