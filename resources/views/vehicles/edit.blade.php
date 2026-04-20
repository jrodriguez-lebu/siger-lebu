@extends('layouts.app')
@section('title', 'Editar Vehículo')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Editar: {{ $vehicle->name }}</h2>
            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('vehicles.update', $vehicle->id) }}" class="space-y-6">
                @csrf @method('PUT')

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Información básica</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nombre / Identificador <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $vehicle->name) }}"
                               class="form-input @error('name') border-red-500 @enderror">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Placa <span class="text-red-500">*</span></label>
                        <input type="text" name="plate" value="{{ old('plate', $vehicle->plate) }}"
                               class="form-input font-mono @error('plate') border-red-500 @enderror">
                        @error('plate') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Tipo <span class="text-red-500">*</span></label>
                        <select name="type" class="form-select @error('type') border-red-500 @enderror">
                            @foreach(['ambulancia' => 'Ambulancia','camion_bomberos' => 'Camión de Bomberos','camioneta' => 'Camioneta','furgon' => 'Furgón','moto' => 'Motocicleta','helicoptero' => 'Helicóptero','bote' => 'Bote','otro' => 'Otro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type', $vehicle->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Estado <span class="text-red-500">*</span></label>
                        <select name="status" class="form-select @error('status') border-red-500 @enderror">
                            @foreach(['disponible' => 'Disponible','en_servicio' => 'En Servicio','mantenimiento' => 'Mantenimiento','fuera_de_servicio' => 'Fuera de Servicio'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $vehicle->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Marca</label>
                        <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Modelo</label>
                        <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Año</label>
                        <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Color</label>
                        <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Capacidad (personas)</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" min="1" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Equipo asignado</label>
                        <select name="team_id" class="form-select">
                            <option value="">— Sin asignar —</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('team_id', $vehicle->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="border-gray-100">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Datos técnicos</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tipo de combustible</label>
                        <input type="text" name="fuel_type" value="{{ old('fuel_type', $vehicle->fuel_type) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Kilometraje actual</label>
                        <input type="number" name="current_mileage" value="{{ old('current_mileage', $vehicle->current_mileage) }}" min="0" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">ID GPS</label>
                        <input type="text" name="gps_tracking_id" value="{{ old('gps_tracking_id', $vehicle->gps_tracking_id) }}" class="form-input">
                    </div>
                </div>

                <hr class="border-gray-100">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Fechas de vencimiento</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Último servicio</label>
                        <input type="date" name="last_service_date" value="{{ old('last_service_date', $vehicle->last_service_date?->format('Y-m-d')) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Próximo servicio</label>
                        <input type="date" name="next_service_date" value="{{ old('next_service_date', $vehicle->next_service_date?->format('Y-m-d')) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Vencimiento seguro</label>
                        <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry', $vehicle->insurance_expiry?->format('Y-m-d')) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Vencimiento revisión técnica</label>
                        <input type="date" name="technical_review_expiry" value="{{ old('technical_review_expiry', $vehicle->technical_review_expiry?->format('Y-m-d')) }}" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Notas</label>
                    <textarea name="notes" rows="3" class="form-input">{{ old('notes', $vehicle->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
