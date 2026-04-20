@extends('layouts.app')
@section('title', 'Editar ' . $personnel->name)
@section('page-title', 'Editar Personal')
@section('page-subtitle', $personnel->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">{{ $personnel->name }}</h2>
            <a href="{{ route('personnel.show', $personnel->id) }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('personnel.update', $personnel->id) }}" class="space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nombre completo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $personnel->name) }}" class="form-input @error('name') border-red-500 @enderror">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">RUT</label>
                        <input type="text" name="rut" value="{{ old('rut', $personnel->rut) }}" class="form-input @error('rut') border-red-500 @enderror" placeholder="12.345.678-9">
                        @error('rut') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Especialidad <span class="text-red-500">*</span></label>
                        <select name="specialty" class="form-select @error('specialty') border-red-500 @enderror">
                            @foreach([
                                'bombero'=>'🔴 Bombero','paramedico'=>'🚑 Paramédico','enfermero'=>'🏥 Enfermero/a',
                                'medico'=>'⚕️ Médico','rescatista'=>'🆘 Rescatista','logistica'=>'📦 Logística',
                                'comunicaciones'=>'📡 Comunicaciones','carabinero'=>'👮 Carabinero',
                                'voluntario'=>'🤝 Voluntario','otro'=>'👤 Otro',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ old('specialty', $personnel->specialty) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('specialty') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Cargo / Función</label>
                        <input type="text" name="position" value="{{ old('position', $personnel->position) }}" class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Equipo</label>
                        <select name="team_id" class="form-select">
                            <option value="">— Sin equipo —</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('team_id', $personnel->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Fecha de ingreso</label>
                        <input type="date" name="joined_date" value="{{ old('joined_date', $personnel->joined_date?->format('Y-m-d')) }}" class="form-input">
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Contacto</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone', $personnel->phone) }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $personnel->email) }}" class="form-input">
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Contacto de emergencia</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nombre</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $personnel->emergency_contact_name) }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $personnel->emergency_contact_phone) }}" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <label class="form-label">Notas</label>
                    <textarea name="notes" rows="2" class="form-input">{{ old('notes', $personnel->notes) }}</textarea>
                </div>

                <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $personnel->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Personal activo</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('personnel.show', $personnel->id) }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">💾 Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
