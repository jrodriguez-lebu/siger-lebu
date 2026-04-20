@extends('layouts.app')
@section('title', $supply->name)
@section('page-title', $supply->name)
@section('page-subtitle', $supply->code ? 'Código: ' . $supply->code : 'Insumo')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            @if($supply->stock_current <= $supply->stock_minimum)
                <span class="badge badge-red">⚠️ Stock bajo</span>
            @endif
            @if($supply->expiry_date && $supply->expiry_date->lte(now()->addDays(30)))
                <span class="badge badge-amber">⏰ Próximo a vencer</span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('supplies.edit', $supply->id) }}" class="btn-secondary">Editar</a>
            <a href="{{ route('supplies.index') }}" class="btn-outline">← Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Información general --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Información General</h3></div>
            <div class="card-body space-y-3">
                @php
                $rows = [
                    'Categoría'  => $supply->getCategoryLabel(),
                    'Unidad'     => $supply->unit,
                    'Ubicación'  => $supply->location ?? '—',
                    'Proveedor'  => $supply->supplier ?? '—',
                    'Costo unit.'=> $supply->unit_cost ? '$' . number_format($supply->unit_cost, 0, ',', '.') : '—',
                    'Equipo'     => $supply->team?->name ?? 'Sin asignar',
                ];
                @endphp
                @foreach($rows as $label => $value)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">{{ $label }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stock --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Stock</h3></div>
            <div class="card-body space-y-4">
                {{-- Barra de stock --}}
                @php
                $pct = $supply->stock_maximum
                    ? min(100, ($supply->stock_current / $supply->stock_maximum) * 100)
                    : ($supply->stock_minimum > 0 ? min(100, ($supply->stock_current / $supply->stock_minimum) * 200) : 100);
                $barColor = $supply->stock_current <= $supply->stock_minimum ? 'bg-red-500' : ($pct < 50 ? 'bg-amber-400' : 'bg-green-500');
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">Stock actual</span>
                        <span class="font-bold text-gray-900">{{ number_format($supply->stock_current, 0) }} {{ $supply->unit }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="{{ $barColor }} h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Mínimo</span>
                    <span class="font-medium text-gray-700">{{ number_format($supply->stock_minimum, 0) }} {{ $supply->unit }}</span>
                </div>
                @if($supply->stock_maximum)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Máximo</span>
                    <span class="font-medium text-gray-700">{{ number_format($supply->stock_maximum, 0) }} {{ $supply->unit }}</span>
                </div>
                @endif
                @if($supply->expiry_date)
                <div class="flex justify-between text-sm border-t border-gray-100 pt-3">
                    <span class="text-gray-500">Vencimiento</span>
                    <span class="font-medium {{ $supply->expiry_date->isPast() ? 'text-red-600' : ($supply->expiry_date->lte(now()->addDays(30)) ? 'text-amber-600' : 'text-gray-900') }}">
                        {{ $supply->expiry_date->format('d/m/Y') }}
                    </span>
                </div>
                @endif
            </div>
        </div>

    </div>

    @if($supply->notes)
    <div class="card">
        <div class="card-body">
            <p class="text-sm text-gray-500 mb-1">Notas</p>
            <p class="text-gray-700 text-sm">{{ $supply->notes }}</p>
        </div>
    </div>
    @endif

    <div class="flex items-center gap-3">
        <form method="POST" action="{{ route('supplies.destroy', $supply->id) }}"
              onsubmit="return confirm('¿Eliminar este insumo?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger">Eliminar</button>
        </form>
    </div>

</div>
@endsection
