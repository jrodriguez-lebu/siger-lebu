@extends('layouts.app')
@section('title', 'Editar Equipamiento')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Editar: {{ $equipment->name }}</h2>
            <a href="{{ route('equipment.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('equipment.update', $equipment->id) }}" class="space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="form-label">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $equipment->name) }}"
                               class="form-input @error('name') border-red-500 @enderror">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Código / Inventario</label>
                        <input type="text" name="code" value="{{ old('code', $equipment->code) }}"
                               class="form-input font-mono @error('code') border-red-500 @enderror">
                        @error('code') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">N° de serie</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}" class="form-input font-mono">
                    </div>

                    <div>
                        <label class="form-label">Categoría <span class="text-red-500">*</span></label>
                        <select name="category" class="form-select @error('category') border-red-500 @enderror">
                            @foreach(['herramienta' => 'Herramienta','equipo_medico' => 'Equipo Médico','equipo_rescate' => 'Equipo de Rescate','comunicacion' => 'Comunicación','proteccion' => 'Protección','otro' => 'Otro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('category', $equipment->category) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Estado <span class="text-red-500">*</span></label>
                        <select name="status" class="form-select @error('status') border-red-500 @enderror">
                            @foreach(['disponible' => 'Disponible','en_uso' => 'En Uso','mantenimiento' => 'Mantenimiento','dado_de_baja' => 'Dado de Baja'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $equipment->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Marca</label>
                        <input type="text" name="brand" value="{{ old('brand', $equipment->brand) }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Modelo</label>
                        <input type="text" name="model" value="{{ old('model', $equipment->model) }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Equipo responsable</label>
                        <select name="team_id" class="form-select">
                            <option value="">— Sin asignar —</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('team_id', $equipment->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Fecha de compra</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Último mantenimiento</label>
                        <input type="date" name="last_maintenance" value="{{ old('last_maintenance', $equipment->last_maintenance?->format('Y-m-d')) }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Próximo mantenimiento</label>
                        <input type="date" name="next_maintenance" value="{{ old('next_maintenance', $equipment->next_maintenance?->format('Y-m-d')) }}" class="form-input">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" rows="2" class="form-input">{{ old('description', $equipment->description) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" rows="2" class="form-input">{{ old('notes', $equipment->notes) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('equipment.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
