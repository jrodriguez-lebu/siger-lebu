@extends('layouts.app')
@section('title', $equipment->name)
@section('page-title', $equipment->name)
@section('page-subtitle', $equipment->code ? 'Código: ' . $equipment->code : 'Equipamiento')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <span class="badge badge-{{ $equipment->getStatusColor() }}">{{ $equipment->getStatusLabel() }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('equipment.edit', $equipment->id) }}" class="btn-secondary">Editar</a>
            <a href="{{ route('equipment.index') }}" class="btn-outline">← Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Info principal --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Información General</h3></div>
            <div class="card-body space-y-3">
                @php
                $rows = [
                    'Categoría'      => $equipment->getCategoryLabel(),
                    'Marca / Modelo' => trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? '')) ?: '—',
                    'N° de Serie'    => $equipment->serial_number ?? '—',
                    'Equipo asignado'=> $equipment->team?->name ?? 'Sin asignar',
                ];
                @endphp
                @foreach($rows as $label => $value)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">{{ $label }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $value }}</span>
                </div>
                @endforeach
                @if($equipment->description)
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Descripción</p>
                    <p class="text-sm text-gray-700">{{ $equipment->description }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Fechas --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Fechas</h3></div>
            <div class="card-body space-y-3">
                @php
                $dates = [
                    'Fecha de compra'        => $equipment->purchase_date,
                    'Último mantenimiento'   => $equipment->last_maintenance,
                    'Próximo mantenimiento'  => $equipment->next_maintenance,
                ];
                @endphp
                @foreach($dates as $label => $date)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">{{ $label }}</span>
                    @if($date)
                        @php $isPast = $date->isPast() && $label !== 'Fecha de compra' && $label !== 'Último mantenimiento'; @endphp
                        <span class="text-sm font-medium {{ $isPast ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $date->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

    </div>

    @if($equipment->notes)
    <div class="card">
        <div class="card-body">
            <p class="text-sm text-gray-500 mb-1">Notas</p>
            <p class="text-gray-700 text-sm">{{ $equipment->notes }}</p>
        </div>
    </div>
    @endif

    {{-- Acciones --}}
    <div class="flex items-center gap-3">
        <form method="POST" action="{{ route('equipment.destroy', $equipment->id) }}"
              onsubmit="return confirm('¿Eliminar este equipamiento?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger">Eliminar</button>
        </form>
    </div>

</div>
@endsection
