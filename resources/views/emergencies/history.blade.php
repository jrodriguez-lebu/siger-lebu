@extends('layouts.app')
@section('title', 'Historial ' . $emergency->folio)
@section('page-title', 'Historial: ' . $emergency->folio)
@section('page-subtitle', 'Registro completo de cambios y acciones')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('emergencies.show', $emergency->id) }}" class="btn-secondary text-sm">← Volver</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">{{ $history->count() }} registros de actividad</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($history as $entry)
            <div class="flex items-start gap-4 px-6 py-4">

                {{-- Avatar / icono --}}
                <div class="flex-shrink-0 flex h-9 w-9 items-center justify-center rounded-full
                    {{ match($entry->action) {
                        'creacion' => 'bg-green-100 text-green-700',
                        'cambio_estado' => 'bg-blue-100 text-blue-700',
                        'asignacion_equipo' => 'bg-purple-100 text-purple-700',
                        'foto_subida' => 'bg-yellow-100 text-yellow-700',
                        'prioridad_cambiada' => 'bg-orange-100 text-orange-700',
                        default => 'bg-gray-100 text-gray-500',
                    } }} text-sm font-bold">
                    {{ match($entry->action) {
                        'creacion' => '✅',
                        'cambio_estado' => '🔄',
                        'asignacion_equipo' => '👥',
                        'foto_subida' => '📷',
                        'prioridad_cambiada' => '⚡',
                        default => '📝',
                    } }}
                </div>

                {{-- Contenido --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <p class="text-sm font-medium text-gray-900">{{ $entry->getActionLabel() }}</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($entry->created_at)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $entry->description }}</p>
                    @if($entry->user)
                    <p class="text-xs text-gray-400 mt-1">
                        Por: <span class="font-medium text-gray-600">{{ $entry->user->name }}</span>
                        ({{ $entry->user->getRoleLabel() }})
                        @if($entry->ip_address) · IP: {{ $entry->ip_address }} @endif
                    </p>
                    @else
                    <p class="text-xs text-gray-400 mt-1">Por: Sistema (Portal Público)</p>
                    @endif

                    {{-- Detalles del cambio --}}
                    @if($entry->old_value && $entry->new_value)
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        <span class="rounded bg-red-50 border border-red-200 px-2 py-0.5 text-red-700 font-mono">
                            {{ is_array(json_decode($entry->old_value, true)) ? implode(', ', json_decode($entry->old_value, true)) : $entry->old_value }}
                        </span>
                        <span class="text-gray-400">→</span>
                        <span class="rounded bg-green-50 border border-green-200 px-2 py-0.5 text-green-700 font-mono">
                            {{ is_array(json_decode($entry->new_value, true)) ? implode(', ', json_decode($entry->new_value, true)) : $entry->new_value }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <p class="text-3xl mb-2">📋</p>
                <p class="text-sm text-gray-400">Sin historial registrado</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
