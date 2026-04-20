@extends('layouts.app')
@section('title', 'Inventario de Insumos')
@section('page-title', 'Reporte de Inventario')
@section('page-subtitle', 'Estado actual del stock de insumos y materiales')

@section('content')
@if($lowStockQty > 0)
<div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 flex items-center gap-3">
    <span class="text-2xl">⚠️</span>
    <p class="text-sm font-medium text-red-800">
        <strong>{{ $lowStockQty }} insumo(s)</strong> con stock por debajo del mínimo requerido.
    </p>
</div>
@endif

<div class="card mb-6">
    <div class="card-body">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="team_id" class="form-select text-sm">
                <option value="">Todos los equipos</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ request('team_id')==$team->id?'selected':'' }}>{{ $team->name }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock')?'checked':'' }} class="rounded">
                Solo stock crítico
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="expiring" value="1" {{ request('expiring')?'checked':'' }} class="rounded">
                Por vencer (30 días)
            </label>
            <button type="submit" class="btn-primary text-sm">Filtrar</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Insumo</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                    <th>Mínimo</th>
                    <th>Estado</th>
                    <th>Equipo</th>
                    <th>Vencimiento</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($supplies as $supply)
                <tr class="{{ $supply->isLowStock() ? 'bg-red-50' : '' }}">
                    <td>
                        <p class="text-sm font-medium text-gray-900">{{ $supply->name }}</p>
                        @if($supply->code)<p class="text-xs text-gray-400">{{ $supply->code }}</p>@endif
                    </td>
                    <td class="text-xs text-gray-600">{{ $supply->getCategoryLabel() }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-20 h-2 rounded-full bg-gray-200 overflow-hidden">
                                <div class="h-full rounded-full {{ $supply->isLowStock() ? 'bg-red-500' : 'bg-green-500' }}"
                                     style="width: {{ min($supply->getStockPercentage(), 100) }}%"></div>
                            </div>
                            <span class="text-sm font-medium {{ $supply->isLowStock() ? 'text-red-700' : 'text-gray-900' }}">
                                {{ $supply->stock_current }} {{ $supply->unit }}
                            </span>
                        </div>
                    </td>
                    <td class="text-sm text-gray-600">{{ $supply->stock_minimum }} {{ $supply->unit }}</td>
                    <td>
                        @if($supply->isLowStock())
                            <span class="badge badge-red">Stock Bajo</span>
                        @else
                            <span class="badge badge-green">Normal</span>
                        @endif
                    </td>
                    <td class="text-xs text-gray-600">{{ $supply->team?->name ?? '—' }}</td>
                    <td>
                        @if($supply->expiry_date)
                            <span class="text-xs {{ $supply->isExpiringSoon() ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                {{ $supply->expiry_date->format('d/m/Y') }}
                                @if($supply->isExpiringSoon()) ⚠️ @endif
                            </span>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-10 text-gray-400">Sin insumos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
