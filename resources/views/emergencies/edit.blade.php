@extends('layouts.app')
@section('title', 'Editar Emergencia')
@section('page-title', 'Editar ' . $emergency->folio)
@section('page-subtitle', 'Modificar datos de la emergencia')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">{{ $emergency->folio }}</h2>
            <a href="{{ route('emergencies.show', $emergency->id) }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('emergencies.update', $emergency->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tipo de emergencia <span class="text-red-500">*</span></label>
                        <select name="type" class="form-select @error('type') border-red-500 @enderror">
                            @foreach(['incendio'=>'🔥 Incendio','accidente_transito'=>'🚗 Accidente de Tránsito','rescate'=>'🆘 Rescate','inundacion'=>'🌊 Inundación','emergencia_medica'=>'🏥 Emergencia Médica','derrumbe'=>'⛰️ Derrumbe','otro'=>'⚠️ Otro'] as $val=>$label)
                                <option value="{{ $val }}" {{ old('type', $emergency->type)==$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Prioridad <span class="text-red-500">*</span></label>
                        <select name="priority" class="form-select @error('priority') border-red-500 @enderror">
                            @foreach(['baja'=>'🟢 Baja','media'=>'🟡 Media','alta'=>'🟠 Alta','critica'=>'🔴 Crítica'] as $val=>$label)
                                <option value="{{ $val }}" {{ old('priority', $emergency->priority)==$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Título <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $emergency->title) }}"
                           class="form-input @error('title') border-red-500 @enderror">
                    @error('title')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Descripción <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" class="form-input @error('description') border-red-500 @enderror">{{ old('description', $emergency->description) }}</textarea>
                    @error('description')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Dirección <span class="text-red-500">*</span></label>
                        <input type="text" name="address" id="address" value="{{ old('address', $emergency->address) }}" class="form-input @error('address') border-red-500 @enderror">
                        @error('address')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Sector</label>
                        <input type="text" name="sector" value="{{ old('sector', $emergency->sector) }}" class="form-input">
                    </div>
                </div>

                {{-- Mapa picker --}}
                <div>
                    <label class="form-label">Ubicación geográfica</label>
                    <p class="text-xs text-gray-400 mb-2">Haz clic en el mapa o arrastra el marcador para ajustar la ubicación</p>
                    <div id="location-map" class="w-full rounded-lg border border-gray-200 overflow-hidden" style="height: 320px;"></div>
                    <div id="coords-display" class="mt-2 {{ old('latitude', $emergency->latitude) ? '' : 'hidden' }} text-xs text-gray-500 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 bg-green-50 border border-green-200 rounded px-2 py-1 text-green-700">
                            📍 <span id="coords-text">{{ old('latitude', $emergency->latitude) }}, {{ old('longitude', $emergency->longitude) }}</span>
                        </span>
                        <button type="button" id="clear-marker" class="text-red-500 hover:text-red-700">✕ Quitar marcador</button>
                    </div>
                    <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude', $emergency->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $emergency->longitude) }}">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Reportado por</label>
                        <input type="text" name="reported_by_name" value="{{ old('reported_by_name', $emergency->reported_by_name) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="reported_by_phone" value="{{ old('reported_by_phone', $emergency->reported_by_phone) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Personas afectadas <span class="text-red-500">*</span></label>
                        <input type="number" name="affected_people" value="{{ old('affected_people', $emergency->affected_people) }}" min="0" class="form-input">
                        @error('affected_people')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Equipo asignado</label>
                    <select name="assigned_team_id" class="form-select">
                        <option value="">— Sin asignar —</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('assigned_team_id', $emergency->assigned_team_id)==$team->id?'selected':'' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Notas internas</label>
                    <textarea name="notes" rows="2" class="form-input">{{ old('notes', $emergency->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('emergencies.show', $emergency->id) }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">💾 Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const LEBU = [-37.6089, -73.6524];
    const latInput  = document.getElementById('latitude');
    const lngInput  = document.getElementById('longitude');
    const display   = document.getElementById('coords-display');
    const coordsTxt = document.getElementById('coords-text');
    const clearBtn  = document.getElementById('clear-marker');

    const initLat = parseFloat(latInput.value);
    const initLng = parseFloat(lngInput.value);
    const center  = (!isNaN(initLat) && !isNaN(initLng)) ? [initLat, initLng] : LEBU;
    const zoom    = (!isNaN(initLat)) ? 16 : 14;

    const map = L.map('location-map').setView(center, zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19,
    }).addTo(map);

    const markerIcon = L.divIcon({
        html: '<div style="background:#dc2626;width:24px;height:24px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4)"></div>',
        iconSize: [24, 24], iconAnchor: [12, 24], className: '',
    });

    let marker = null;

    function setMarker(lat, lng) {
        if (marker) marker.remove();
        marker = L.marker([lat, lng], { icon: markerIcon, draggable: true }).addTo(map);
        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateInputs(pos.lat, pos.lng);
        });
        updateInputs(lat, lng);
    }

    function updateInputs(lat, lng) {
        latInput.value  = lat.toFixed(6);
        lngInput.value  = lng.toFixed(6);
        coordsTxt.textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
        display.classList.remove('hidden');
    }

    map.on('click', function (e) { setMarker(e.latlng.lat, e.latlng.lng); });

    clearBtn.addEventListener('click', function () {
        if (marker) { marker.remove(); marker = null; }
        latInput.value = lngInput.value = '';
        display.classList.add('hidden');
    });

    if (!isNaN(initLat) && !isNaN(initLng)) setMarker(initLat, initLng);
})();
</script>
@endpush
