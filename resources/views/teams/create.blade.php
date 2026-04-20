@extends('layouts.app')
@section('title', 'Nuevo Equipo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Nuevo Equipo</h2>
            <a href="{{ route('teams.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('teams.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="form-label">Nombre del equipo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-input @error('name') border-red-500 @enderror"
                               placeholder="Ej: Equipo Rescate Alpha">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Tipo <span class="text-red-500">*</span></label>
                        <select name="type" class="form-select @error('type') border-red-500 @enderror">
                            <option value="">— Selecciona —</option>
                            <option value="rescate"  {{ old('type') === 'rescate'  ? 'selected' : '' }}>Rescate</option>
                            <option value="tecnico"  {{ old('type') === 'tecnico'  ? 'selected' : '' }}>Técnico</option>
                            <option value="bombero"  {{ old('type') === 'bombero'  ? 'selected' : '' }}>Bomberos</option>
                            <option value="medico"   {{ old('type') === 'medico'   ? 'selected' : '' }}>Médico</option>
                            <option value="apoyo"    {{ old('type') === 'apoyo'    ? 'selected' : '' }}>Apoyo</option>
                        </select>
                        @error('type') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Estado <span class="text-red-500">*</span></label>
                        <select name="status" class="form-select @error('status') border-red-500 @enderror">
                            <option value="activo"   {{ old('status', 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('status') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            <option value="disuelto" {{ old('status') === 'disuelto' ? 'selected' : '' }}>Disuelto</option>
                        </select>
                        @error('status') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Líder del equipo</label>
                        <select name="leader_id" class="form-select @error('leader_id') border-red-500 @enderror">
                            <option value="">— Sin líder asignado —</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}" {{ old('leader_id') == $leader->id ? 'selected' : '' }}>
                                    {{ $leader->name }} ({{ $leader->getRoleLabel() }})
                                </option>
                            @endforeach
                        </select>
                        @error('leader_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Color identificador</label>
                        <input type="color" name="color" value="{{ old('color', '#1e40af') }}"
                               class="form-input h-10 cursor-pointer">
                        @error('color') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" rows="3"
                                  class="form-input @error('description') border-red-500 @enderror"
                                  placeholder="Descripción del equipo, especialidades, área de cobertura...">{{ old('description') }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('teams.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Crear Equipo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
