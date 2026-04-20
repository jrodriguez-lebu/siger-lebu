@extends('layouts.guest')
@section('title', 'Mapa de Emergencias')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #public-map {
        height: calc(100vh - 64px);
        width: 100%;
    }
    .legend-box {
        background: white;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        font-size: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .legend-row { display: flex; align-items: center; gap: 8px; margin-bottom: 5px; }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
    .info-panel {
        position: absolute;
        top: 80px;
        left: 16px;
        z-index: 1000;
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        padding: 16px;
        width: 260px;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<div style="position: relative;">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 px-4 h-16 flex items-center justify-between" style="position:relative;z-index:1001;">
        <div class="flex items-center gap-3">
            <a href="{{ route('public.index') }}" class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-600 text-white font-bold text-sm">SG</div>
                <div>
                    <p class="font-bold text-gray-900 text-sm leading-tight">SIGER</p>
                    <p class="text-xs text-gray-500 leading-tight">Municipalidad de Lebu</p>
                </div>
            </a>
            <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                En vivo
            </span>
        </div>
        <div class="flex items-center gap-3">
            <span id="emergency-count" class="text-sm text-gray-600 hidden sm:block"></span>
            <a href="{{ route('public.report') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                🚨 Reportar
            </a>
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                Portal Interno →
            </a>
        </div>
    </header>

    {{-- Panel lateral de emergencias --}}
    <div class="info-panel" id="info-panel">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Emergencias Activas</p>
        <div id="emergency-list">
            <div class="text-center py-4">
                <div class="inline-block h-5 w-5 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></div>
                <p class="text-xs text-gray-400 mt-2">Cargando...</p>
            </div>
        </div>
    </div>

    {{-- Mapa --}}
    <div id="public-map"></div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Mapa centrado en Lebu, Chile
const map = L.map('public-map', { zoomControl: true }).setView([-37.6069, -73.6531], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
    maxZoom: 19,
}).addTo(map);

// Leyenda
const legend = L.control({ position: 'bottomright' });
legend.onAdd = () => {
    const div = L.DomUtil.create('div', 'legend-box');
    div.innerHTML = `
        <p style="font-weight:600;color:#374151;margin-bottom:8px;font-size:11px;">PRIORIDAD</p>
        <div class="legend-row"><div class="legend-dot" style="background:#dc2626"></div><span style="color:#374151">Crítica</span></div>
        <div class="legend-row"><div class="legend-dot" style="background:#ea580c"></div><span style="color:#374151">Alta</span></div>
        <div class="legend-row"><div class="legend-dot" style="background:#ca8a04"></div><span style="color:#374151">Media</span></div>
        <div class="legend-row"><div class="legend-dot" style="background:#16a34a"></div><span style="color:#374151">Baja</span></div>
    `;
    return div;
};
legend.addTo(map);

let markers = [];

function createMarkerIcon(color, icon) {
    return L.divIcon({
        className: '',
        html: `<div style="
            background:${color};width:34px;height:34px;
            border-radius:50% 50% 50% 0;transform:rotate(-45deg);
            border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);
            display:flex;align-items:center;justify-content:center;">
            <span style="transform:rotate(45deg);font-size:15px;line-height:1;">${icon}</span>
        </div>`,
        iconSize: [34, 34],
        iconAnchor: [17, 34],
        popupAnchor: [0, -36],
    });
}

function loadEmergencies() {
    fetch('/api/emergencias-geojson')
        .then(r => r.json())
        .then(data => {
            // Limpiar marcadores anteriores
            markers.forEach(m => map.removeLayer(m));
            markers = [];

            const features = data.features || [];
            const count = features.length;

            // Actualizar contador
            const countEl = document.getElementById('emergency-count');
            if (countEl) {
                countEl.textContent = count === 0
                    ? 'Sin emergencias activas'
                    : `${count} emergencia${count > 1 ? 's' : ''} activa${count > 1 ? 's' : ''}`;
            }

            // Lista lateral
            const listEl = document.getElementById('emergency-list');
            if (count === 0) {
                listEl.innerHTML = `
                    <div class="text-center py-6">
                        <p style="font-size:2rem">✅</p>
                        <p style="font-size:12px;color:#6b7280;margin-top:6px;">Sin emergencias activas en este momento</p>
                    </div>`;
            } else {
                listEl.innerHTML = features.map(f => `
                    <div class="emergency-item" onclick="focusMarker(${f.geometry.coordinates[1]}, ${f.geometry.coordinates[0]})"
                         style="padding:10px 8px;border-radius:8px;cursor:pointer;margin-bottom:4px;transition:background 0.15s;"
                         onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-size:1.25rem">${f.properties.type_icon}</span>
                            <div style="min-width:0;">
                                <p style="font-size:12px;font-weight:600;color:#111827;">${f.properties.folio}</p>
                                <p style="font-size:11px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${f.properties.address}</p>
                                <span style="display:inline-block;background:#dbeafe;color:#1e40af;border-radius:999px;padding:1px 8px;font-size:10px;margin-top:2px;">${f.properties.status_label}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // Agregar marcadores al mapa
            features.forEach(f => {
                const [lng, lat] = f.geometry.coordinates;
                const p = f.properties;

                const marker = L.marker([lat, lng], {
                    icon: createMarkerIcon(p.color, p.type_icon)
                }).addTo(map);

                marker.bindPopup(`
                    <div style="min-width:200px;font-family:sans-serif;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                            <span style="font-size:1.5rem">${p.type_icon}</span>
                            <div>
                                <p style="font-weight:700;font-size:13px;color:#111827;margin:0;">${p.folio}</p>
                                <p style="font-size:11px;color:#6b7280;margin:0;">${p.type_label}</p>
                            </div>
                        </div>
                        <p style="font-size:12px;color:#374151;margin-bottom:6px;">📍 ${p.address}</p>
                        <p style="font-size:12px;color:#374151;margin-bottom:6px;">👥 ${p.affected} persona(s) afectada(s)</p>
                        ${p.team ? `<p style="font-size:12px;color:#374151;margin-bottom:6px;">🚒 Equipo: ${p.team}</p>` : ''}
                        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                            <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:999px;font-size:11px;">${p.status_label}</span>
                        </div>
                        <p style="font-size:11px;color:#9ca3af;">${p.created_at}</p>
                    </div>
                `, { maxWidth: 280 });

                markers.push(marker);
            });

            // Ajustar vista si hay emergencias
            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.3));
            }
        })
        .catch(() => {
            document.getElementById('emergency-list').innerHTML =
                '<p style="font-size:12px;color:#ef4444;text-align:center;padding:16px;">Error al cargar emergencias</p>';
        });
}

function focusMarker(lat, lng) {
    map.setView([lat, lng], 16);
    markers.forEach(m => {
        const pos = m.getLatLng();
        if (Math.abs(pos.lat - lat) < 0.0001 && Math.abs(pos.lng - lng) < 0.0001) {
            m.openPopup();
        }
    });
}

// Carga inicial y refresco cada 30 segundos
loadEmergencies();
setInterval(loadEmergencies, 30000);
</script>
@endpush
