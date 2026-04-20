@extends('layouts.app')
@section('title', 'Nuevo Insumo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Nuevo Insumo</h2>
            <a href="{{ route('supplies.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('supplies.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="form-label">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-input @error('name') border-red-500 @enderror"
                               placeholder="Ej: Vendas elásticas 10cm">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Código</label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               class="form-input font-mono @error('code') border-red-500 @enderror"
                               placeholder="INS-001">
                        @error('code') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Categoría <span class="text-red-500">*</span></label>
                        <select name="category" class="form-select @error('category') border-red-500 @enderror">
                            <option value="">— Selecciona —</option>
                            @foreach(['medicamento' => 'Medicamento','material_curacion' => 'Material de Curación','oxigeno' => 'Oxígeno','combustible' => 'Combustible','alimento' => 'Alimento','ropa' => 'Ropa/Vestuario','herramienta' => 'Herramienta','otro' => 'Otro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Unidad de medida <span class="text-red-500">*</span></label>
                        <input type="text" name="unit" value="{{ old('unit') }}"
                               class="form-input @error('unit') border-red-500 @enderror"
                               placeholder="unidades, kg, litros...">
                        @error('unit') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Stock actual <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_current" value="{{ old('stock_current', 0) }}" min="0" step="0.1"
                               class="form-input @error('stock_current') border-red-500 @enderror">
                        @error('stock_current') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Stock mínimo <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_minimum" value="{{ old('stock_minimum', 0) }}" min="0" step="0.1"
                               class="form-input @error('stock_minimum') border-red-500 @enderror">
                        @error('stock_minimum') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Stock máximo</label>
                        <input type="number" name="stock_maximum" value="{{ old('stock_maximum') }}" min="0" step="0.1" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Equipo responsable</label>
                        <select name="team_id" class="form-select">
                            <option value="">— Sin asignar —</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Ubicación</label>
                        <input type="text" name="location" value="{{ old('location') }}" class="form-input" placeholder="Bodega A, Estante 2...">
                    </div>

                    <div>
                        <label class="form-label">Fecha de vencimiento</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Proveedor</label>
                        <input type="text" name="supplier" value="{{ old('supplier') }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Costo unitario</label>
                        <input type="number" name="unit_cost" value="{{ old('unit_cost') }}" min="0" step="0.01" class="form-input" placeholder="0.00">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" rows="2" class="form-input" placeholder="Observaciones adicionales...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('supplies.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Crear Insumo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
