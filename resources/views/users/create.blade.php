@extends('layouts.app')
@section('title', 'Nuevo Usuario')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Nuevo Usuario</h2>
            <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="form-label">Nombre completo <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-input @error('name') border-red-500 @enderror"
                           placeholder="Juan Pérez González">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Correo electrónico <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-input @error('email') border-red-500 @enderror"
                           placeholder="usuario@siger.cl">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+56 9 1234 5678">
                </div>

                <div>
                    <label class="form-label">Rol <span class="text-red-500">*</span></label>
                    <select name="role" class="form-select @error('role') border-red-500 @enderror">
                        <option value="">— Selecciona un rol —</option>
                        <option value="admin"       {{ old('role') === 'admin'       ? 'selected' : '' }}>Super Admin</option>
                        <option value="coordinador" {{ old('role') === 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                        <option value="lider"       {{ old('role') === 'lider'       ? 'selected' : '' }}>Líder de Equipo</option>
                        <option value="digitador"   {{ old('role') === 'digitador'   ? 'selected' : '' }}>Digitador</option>
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Contraseña <span class="text-red-500">*</span></label>
                        <input type="password" name="password"
                               class="form-input @error('password') border-red-500 @enderror">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Confirmar contraseña <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" class="form-input">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="active" value="1" id="active"
                           {{ old('active', '1') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600">
                    <label for="active" class="text-sm text-gray-700">Cuenta activa</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('users.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
