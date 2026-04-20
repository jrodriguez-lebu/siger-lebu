@extends('layouts.app')
@section('title', 'Nuevo Personal')
@section('page-title', 'Agregar Personal')
@section('page-subtitle', 'Registrar una nueva persona en el sistema')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Datos del personal</h2>
            <a href="{{ route('personnel.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('personnel.store') }}" class="space-y-5">
                @csrf

                {{-- Identificación --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nombre completo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-input @error('name') border-red-500 @enderror"
                               placeholder="Juan Pérez González">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">RUT</label>
                        <input type="text" name="rut" value="{{ old('rut') }}" class="form-input @error('rut') border-red-500 @enderror"
                               placeholder="12.345.678-9">
                        @error('rut') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Rol y equipo --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Especialidad <span class="text-red-500">*</span></label>
                        <select name="specialty" class="form-select @error('specialty') border-red-500 @enderror">
                            @foreach([
                                'bombero'        => '🔴 Bombero',
                                'paramedico'     => '🚑 Paramédico',
                                'enfermero'      => '🏥 Enfermero/a',
                                'medico'         => '⚕️ Médico',
                                'rescatista'     => '🆘 Rescatista',
                                'logistica'      => '📦 Logística',
                                'comunicaciones' => '📡 Comunicaciones',
                                'carabinero'     => '👮 Carabinero',
                                'voluntario'     => '🤝 Voluntario',
                                'otro'           => '👤 Otro',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ old('specialty') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('specialty') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Cargo / Función</label>
                        <input type="text" name="position" value="{{ old('position') }}" class="form-input"
                               placeholder="Ej: Jefe de carro, Rescatista 1...">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Equipo</label>
                        <select name="team_id" class="form-select">
                            <option value="">— Sin equipo —</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('team_id', $defaultTeamId) == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Fecha de ingreso</label>
                        <input type="date" name="joined_date" value="{{ old('joined_date') }}" class="form-input">
                    </div>
                </div>

                {{-- Contacto --}}
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Datos de contacto</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+56 9 1234 5678">
                        </div>
                        <div>
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="juan@ejemplo.cl">
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Contacto de emergencia --}}
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Contacto de emergencia</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nombre</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="form-input" placeholder="Familiar o contacto">
                        </div>
                        <div>
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="form-input" placeholder="+56 9...">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <label class="form-label">Notas</label>
                    <textarea name="notes" rows="2" class="form-input" placeholder="Información adicional...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Personal activo</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('personnel.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">💾 Guardar Personal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
