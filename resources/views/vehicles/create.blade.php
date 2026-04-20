@extends('layouts.app')
@section('title', 'Nuevo Vehículo')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Nuevo Vehículo</h2>
            <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-6">
                @csrf

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Información básica</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nombre / Identificador <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-input @error('name') border-red-500 @enderror"
                               placeholder="Ej: Ambulancia 01">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Placa <span class="text-red-500">*</span></label>
                        <input type="text" name="plate" value="{{ old('plate') }}"
                               class="form-input font-mono @error('plate') border-red-500 @enderror"
                               placeholder="AA-BB-00">
                        @error('plate') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Tipo <span class="text-red-500">*</span></label>
                        <select name="type" class="form-select @error('type') border-red-500 @enderror">
                            <option value="">— Selecciona —</option>
                            @foreach(['ambulancia' => 'Ambulancia','camion_bomberos' => 'Camión de Bomberos','camioneta' => 'Camioneta','furgon' => 'Furgón','moto' => 'Motocicleta','helicoptero' => 'Helicóptero','bote' => 'Bote','otro' => 'Otro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Estado <span class="text-red-500">*</span></label>
                        <select name="status" class="form-select @error('status') border-red-500 @enderror">
                            <option value="disponible"        {{ old('status','disponible') === 'disponible'        ? 'selected' : '' }}>Disponible</option>
                            <option value="en_servicio"       {{ old('status') === 'en_servicio'       ? 'selected' : '' }}>En Servicio</option>
                            <option value="mantenimiento"     {{ old('status') === 'mantenimiento'     ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="fuera_de_servicio" {{ old('status') === 'fuera_de_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                        </select>
                        @error('status') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Marca</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" class="form-input" placeholder="Ford, Toyota...">
                    </div>
                    <div>
                        <label class="form-label">Modelo</label>
                        <input type="text" name="model" value="{{ old('model') }}" class="form-input" placeholder="Transit, Hilux...">
                    </div>
                    <div>
                        <label class="form-label">Año</label>
                        <input type="number" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}" class="form-input" placeholder="{{ date('Y') }}">
                    </div>
                    <div>
                        <label class="form-label">Color</label>
                        <input type="text" name="color" value="{{ old('color') }}" class="form-input" placeholder="Blanco, Rojo...">
                    </div>
                    <div>
                        <label class="form-label">Capacidad (personas)</label>
                        <input type="number" name="capacity" value="{{ old('capacity') }}" min="1" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Equipo asignado</label>
                        <select name="team_id" class="form-select">
                            <option value="">— Sin asignar —</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="border-gray-100">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Datos técnicos</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tipo de combustible</label>
                        <input type="text" name="fuel_type" value="{{ old('fuel_type') }}" class="form-input" placeholder="Bencina, Diesel...">
                    </div>
                    <div>
                        <label class="form-label">Kilometraje actual</label>
                        <input type="number" name="current_mileage" value="{{ old('current_mileage') }}" min="0" class="form-input" placeholder="0">
                    </div>
                    <div>
                        <label class="form-label">ID GPS</label>
                        <input type="text" name="gps_tracking_id" value="{{ old('gps_tracking_id') }}" class="form-input">
                    </div>
                </div>

                <hr class="border-gray-100">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Fechas de vencimiento</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Último servicio</label>
                        <input type="date" name="last_service_date" value="{{ old('last_service_date') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Próximo servicio</label>
                        <input type="date" name="next_service_date" value="{{ old('next_service_date') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Vencimiento seguro</label>
                        <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Vencimiento revisión técnica</label>
                        <input type="date" name="technical_review_expiry" value="{{ old('technical_review_expiry') }}" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Notas</label>
                    <textarea name="notes" rows="3" class="form-input" placeholder="Observaciones adicionales...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('vehicles.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Crear Vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
