@extends('layouts.app')
@section('title', $vehicle->name)

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $vehicle->name }}</h1>
            <p class="text-sm text-gray-500">{{ $vehicle->getTypeLabel() }} — Placa: <span class="font-mono font-semibold">{{ $vehicle->plate }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn-secondary">Editar</a>
            <a href="{{ route('vehicles.index') }}" class="btn-outline">← Volver</a>
        </div>
    </div>

    {{-- Alertas de vencimiento --}}
    @if($vehicle->isInsuranceExpiringSoon() || $vehicle->isTechnicalReviewExpiringSoon())
    <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 space-y-1">
        <p class="font-semibold text-amber-800">⚠️ Alertas de vencimiento</p>
        @if($vehicle->isInsuranceExpiringSoon())
            <p class="text-sm text-amber-700">El seguro vence el {{ $vehicle->insurance_expiry->format('d/m/Y') }}.</p>
        @endif
        @if($vehicle->isTechnicalReviewExpiringSoon())
            <p class="text-sm text-amber-700">La revisión técnica vence el {{ $vehicle->technical_review_expiry->format('d/m/Y') }}.</p>
        @endif
    </div>
    @endif

    {{-- Info principal --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Información General</h3></div>
            <div class="card-body space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Estado</span>
                    <span class="badge badge-{{ $vehicle->getStatusColor() }}">{{ $vehicle->getStatusLabel() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Marca / Modelo</span>
                    <span class="text-sm font-medium text-gray-900">{{ $vehicle->brand ?? '—' }} {{ $vehicle->model ?? '' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Año</span>
                    <span class="text-sm font-medium text-gray-900">{{ $vehicle->year ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Color</span>
                    <span class="text-sm font-medium text-gray-900">{{ $vehicle->color ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Capacidad</span>
                    <span class="text-sm font-medium text-gray-900">{{ $vehicle->capacity ? $vehicle->capacity . ' personas' : '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Combustible</span>
                    <span class="text-sm font-medium text-gray-900">{{ $vehicle->fuel_type ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Kilometraje</span>
                    <span class="text-sm font-medium text-gray-900">{{ $vehicle->current_mileage ? number_format($vehicle->current_mileage) . ' km' : '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Equipo</span>
                    <span class="text-sm font-medium text-gray-900">{{ $vehicle->team?->name ?? 'Sin asignar' }}</span>
                </div>
                @if($vehicle->gps_tracking_id)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">GPS ID</span>
                    <span class="text-sm font-mono text-gray-900">{{ $vehicle->gps_tracking_id }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Fechas Importantes</h3></div>
            <div class="card-body space-y-3">
                @php
                    $dates = [
                        'Último servicio'        => $vehicle->last_service_date,
                        'Próximo servicio'        => $vehicle->next_service_date,
                        'Vencimiento seguro'      => $vehicle->insurance_expiry,
                        'Revisión técnica'        => $vehicle->technical_review_expiry,
                    ];
                @endphp
                @foreach($dates as $label => $date)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">{{ $label }}</span>
                    @if($date)
                        @php $isPast = $date->isPast(); $isSoon = !$isPast && $date->lte(now()->addDays(30)); @endphp
                        <span class="text-sm font-medium {{ $isPast ? 'text-red-600' : ($isSoon ? 'text-amber-600' : 'text-gray-900') }}">
                            {{ $date->format('d/m/Y') }}
                            @if($isPast) <span class="text-xs">(vencido)</span> @elseif($isSoon) <span class="text-xs">(próximo)</span> @endif
                        </span>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @if($vehicle->notes)
    <div class="card">
        <div class="card-body">
            <p class="text-sm text-gray-500 mb-1">Notas</p>
            <p class="text-gray-700">{{ $vehicle->notes }}</p>
        </div>
    </div>
    @endif

    {{-- Historial de emergencias --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Historial de emergencias</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Dirección</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicle->emergencies as $emergency)
                    <tr>
                        <td>
                            <a href="{{ route('emergencies.show', $emergency) }}" class="font-mono text-blue-600 hover:underline">
                                {{ $emergency->folio }}
                            </a>
                        </td>
                        <td>{{ $emergency->getTypeLabel() }}</td>
                        <td><span class="badge badge-{{ $emergency->getStatusColor() }}">{{ $emergency->getStatusLabel() }}</span></td>
                        <td class="text-sm text-gray-600">{{ $emergency->address }}</td>
                        <td class="text-sm text-gray-500">{{ $emergency->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-400 py-6">Sin emergencias registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
