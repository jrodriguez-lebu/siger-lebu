@extends('layouts.app')
@section('title', 'Editar Usuario')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Editar: {{ $user->name }}</h2>
            <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="form-label">Nombre completo <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-input @error('name') border-red-500 @enderror">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Correo electrónico <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-input @error('email') border-red-500 @enderror">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Rol <span class="text-red-500">*</span></label>
                    <select name="role" class="form-select @error('role') border-red-500 @enderror">
                        <option value="admin"       {{ old('role', $user->role) === 'admin'       ? 'selected' : '' }}>Super Admin</option>
                        <option value="coordinador" {{ old('role', $user->role) === 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                        <option value="lider"       {{ old('role', $user->role) === 'lider'       ? 'selected' : '' }}>Líder de Equipo</option>
                        <option value="digitador"   {{ old('role', $user->role) === 'digitador'   ? 'selected' : '' }}>Digitador</option>
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nueva contraseña <span class="text-gray-400 font-normal">(dejar vacío para no cambiar)</span></label>
                        <input type="password" name="password"
                               class="form-input @error('password') border-red-500 @enderror">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="form-input">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="active" value="1" id="active"
                           {{ old('active', $user->active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600">
                    <label for="active" class="text-sm text-gray-700">Cuenta activa</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('users.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
