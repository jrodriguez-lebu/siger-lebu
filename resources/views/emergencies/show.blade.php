@extends('layouts.app')
@section('title', $emergency->folio)
@section('page-title', $emergency->folio)
@section('page-subtitle', $emergency->title)

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Columna principal ────────────────────────────── --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Info general --}}
        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">{{ $emergency->getTypeIcon() }}</span>
                    <div>
                        <h2 class="font-semibold text-gray-900">{{ $emergency->title }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="badge badge-{{ $emergency->getStatusColor() }}">{{ $emergency->getStatusLabel() }}</span>
                            <span class="badge badge-{{ $emergency->getPriorityColor() === 'orange' ? 'yellow' : $emergency->getPriorityColor() }}">{{ $emergency->getPriorityLabel() }}</span>
                            <span class="text-xs text-gray-400">{{ $emergency->getTypeLabel() }}</span>
                        </div>
                    </div>
                </div>
                @if(in_array(auth()->user()->role, ['admin', 'coordinador']))
                <a href="{{ route('emergencies.edit', $emergency->id) }}" class="btn-outline text-xs">Editar</a>
                @endif
            </div>
            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Dirección</p>
                    <p class="text-gray-900 mt-1">{{ $emergency->address }}</p>
                    @if($emergency->sector)<p class="text-gray-500">{{ $emergency->sector }}</p>@endif
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Reportado por</p>
                    <p class="text-gray-900 mt-1">{{ $emergency->reported_by_name ?? 'Portal público' }}</p>
                    @if($emergency->reported_by_phone)<p class="text-gray-500">{{ $emergency->reported_by_phone }}</p>@endif
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Personas afectadas</p>
                    <p class="text-gray-900 mt-1 font-semibold text-lg">{{ $emergency->affected_people }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Fecha de ingreso</p>
                    <p class="text-gray-900 mt-1">{{ $emergency->created_at->format('d/m/Y H:i') }}</p>
                    <p class="text-gray-400 text-xs">{{ $emergency->created_at->diffForHumans() }}</p>
                </div>
                @if($emergency->notes)
                <div class="sm:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Notas</p>
                    <p class="text-gray-700 mt-1">{{ $emergency->notes }}</p>
                </div>
                @endif
                <div class="sm:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Descripción</p>
                    <p class="text-gray-700 mt-1 leading-relaxed">{{ $emergency->description }}</p>
                </div>
            </div>
        </div>

        {{-- Fotos --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Fotografías ({{ $emergency->photos->count() }})</h3>
            </div>
            <div class="card-body">
                @if($emergency->photos->isNotEmpty())
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
                    @foreach($emergency->photos as $photo)
                    <div class="group relative rounded-lg overflow-hidden aspect-square bg-gray-100">
                        <a href="{{ $photo->url }}" target="_blank">
                            <img src="{{ $photo->url }}" alt="{{ $photo->caption }}" class="h-full w-full object-cover group-hover:opacity-90 transition">
                        </a>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 p-2 opacity-0 group-hover:opacity-100 transition">
                            <p class="text-white text-xs truncate">{{ $photo->source }}</p>
                        </div>
                        @if(in_array(auth()->user()->role, ['admin', 'coordinador']))
                        <form method="POST" action="{{ route('emergencies.deletePhoto', [$emergency->id, $photo->id]) }}" class="absolute top-1 right-1">
                            @csrf @method('DELETE')
                            <button class="flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-white text-xs opacity-0 group-hover:opacity-100 transition hover:bg-red-700">✕</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Subida de fotos --}}
                <form method="POST" action="{{ route('emergencies.uploadPhotos', $emergency->id) }}" enctype="multipart/form-data"
                      class="flex items-center gap-3">
                    @csrf
                    <input type="file" name="photos[]" multiple accept="image/*" class="form-input text-sm">
                    <button type="submit" class="btn-primary flex-shrink-0 text-sm">Subir</button>
                </form>
            </div>
        </div>

        {{-- Historial --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Historial de cambios</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($emergency->history->take(10) as $entry)
                <div class="px-6 py-3 flex items-start gap-3">
                    <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600 text-xs font-bold mt-0.5">
                        {{ strtoupper(substr($entry->user?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">{{ $entry->description }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $entry->user?->name ?? 'Sistema' }} · {{ \Carbon\Carbon::parse($entry->created_at)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-6 text-center text-sm text-gray-400">Sin historial registrado</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Columna lateral ──────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Cambio de estado --}}
        @if(count($allowedTransitions) > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 text-sm">Cambiar Estado</h3>
            </div>
            <div class="card-body space-y-2">
                @foreach($allowedTransitions as $newStatus)
                <form method="POST" action="{{ route('emergencies.changeStatus', $emergency->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="{{ $newStatus }}">
                    @php
                    $statusLabels = ['en_proceso' => 'Iniciar Atención', 'atendida' => 'Marcar como Atendida', 'cerrada' => 'Cerrar Emergencia', 'cancelada' => 'Cancelar', 'ingresada' => 'Reabrir'];
                    $btnClass = $newStatus === 'cancelada' ? 'btn-danger' : ($newStatus === 'cerrada' ? 'btn-success' : 'btn-primary');
                    @endphp
                    <button type="submit" class="{{ $btnClass }} w-full justify-center text-sm">
                        {{ $statusLabels[$newStatus] ?? ucfirst($newStatus) }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Asignación de equipo --}}
        @if(in_array(auth()->user()->role, ['admin', 'coordinador']))
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 text-sm">Equipo Asignado</h3>
            </div>
            <div class="card-body">
                @if($emergency->assignedTeam)
                <div class="mb-3 p-3 rounded-lg bg-blue-50 border border-blue-100">
                    <p class="font-medium text-blue-900 text-sm">{{ $emergency->assignedTeam->name }}</p>
                    <p class="text-xs text-blue-600">{{ $emergency->assignedTeam->getTypeLabel() }}</p>
                    @if($emergency->assignedTeam->leader)
                    <p class="text-xs text-blue-500 mt-1">Líder: {{ $emergency->assignedTeam->leader->name }}</p>
                    @endif
                </div>
                @endif
                <form method="POST" action="{{ route('emergencies.assignTeam', $emergency->id) }}" class="flex gap-2">
                    @csrf
                    <select name="assigned_team_id" class="form-select flex-1 text-sm">
                        <option value="">Sin asignar</option>
                        @foreach($availableTeams as $team)
                        <option value="{{ $team->id }}" {{ $emergency->assigned_team_id == $team->id ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary text-sm">Asignar</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Ubicación --}}
        @if($emergency->hasCoordinates())
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 text-sm">Ubicación</h3>
            </div>
            <div class="card-body p-0">
                <div id="mini-map" class="h-48 rounded-b-xl"></div>
            </div>
        </div>
        @endif

        {{-- Meta --}}
        <div class="card">
            <div class="card-body text-xs text-gray-500 space-y-1.5">
                <div class="flex justify-between">
                    <span>Folio:</span>
                    <span class="font-mono font-medium text-gray-900">{{ $emergency->folio }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Creado por:</span>
                    <span class="text-gray-700">{{ $emergency->createdBy?->name ?? 'Portal público' }}</span>
                </div>
                @if($emergency->started_at)
                <div class="flex justify-between">
                    <span>Inicio atención:</span>
                    <span class="text-gray-700">{{ $emergency->started_at->format('d/m H:i') }}</span>
                </div>
                @endif
                @if($emergency->resolved_at)
                <div class="flex justify-between">
                    <span>Resolución:</span>
                    <span class="text-gray-700">{{ $emergency->resolved_at->format('d/m H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

@if($emergency->hasCoordinates())
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('mini-map', { zoomControl: false, dragging: false }).setView(
    [{{ $emergency->latitude }}, {{ $emergency->longitude }}], 15
);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([{{ $emergency->latitude }}, {{ $emergency->longitude }}]).addTo(map)
    .bindPopup('{{ addslashes($emergency->address) }}').openPopup();
</script>
@endpush
@endif
