@extends('layouts.app')
@section('title', 'Mapa Operativo')
@section('page-title', 'Mapa Operativo')
@section('page-subtitle', 'Visualización geolocalizada de emergencias activas')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #map { height: calc(100vh - 180px); min-height: 500px; border-radius: 0.75rem; }
    .emergency-popup { min-width: 220px; }
    .legend { background: white; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb; }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; margin-bottom: 6px; }
    .legend-dot { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="flex gap-4" style="height: calc(100vh - 180px);">

    {{-- Sidebar del mapa --}}
    <div class="w-80 flex-shrink-0 flex flex-col gap-3">

        {{-- Filtros --}}
        <div class="card">
            <div class="card-body space-y-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Filtrar por</p>
                <div>
                    <select id="filter-status" class="form-select text-sm">
                        <option value="">Todos los estados</option>
                        <option value="ingresada">Ingresadas</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="atendida">Atendidas</option>
                    </select>
                </div>
                <div>
                    <select id="filter-priority" class="form-select text-sm">
                        <option value="">Todas las prioridades</option>
                        <option value="critica">Crítica</option>
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
                <button id="apply-filter" class="btn-primary w-full text-sm justify-center">Aplicar</button>
            </div>
        </div>

        {{-- Lista de emergencias --}}
        <div class="card flex-1 overflow-hidden flex flex-col">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-900">Emergencias activas</h3>
                <span id="count-badge" class="badge badge-blue">0</span>
            </div>
            <div id="emergency-list" class="flex-1 overflow-y-auto divide-y divide-gray-50">
                <div class="p-4 text-center text-sm text-gray-400">Cargando...</div>
            </div>
        </div>
    </div>

    {{-- Mapa --}}
    <div class="flex-1">
        <div id="map"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Configuración del mapa centrado en Lebu
const map = L.map('map').setView([-37.6069, -73.6531], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

// Leyenda de prioridades
const legend = L.control({ position: 'bottomright' });
legend.onAdd = function() {
    const div = L.DomUtil.create('div', 'legend');
    div.innerHTML = `
        <p style="font-size:11px;font-weight:600;color:#374151;margin-bottom:8px;">PRIORIDAD</p>
        <div class="legend-item"><div class="legend-dot" style="background:#dc2626"></div>Crítica</div>
        <div class="legend-item"><div class="legend-dot" style="background:#ea580c"></div>Alta</div>
        <div class="legend-item"><div class="legend-dot" style="background:#ca8a04"></div>Media</div>
        <div class="legend-item"><div class="legend-dot" style="background:#16a34a"></div>Baja</div>
    `;
    return div;
};
legend.addTo(map);

// Variables globales
let allFeatures = [];
let markers = {};

// Crear ícono personalizado
function createIcon(color, icon) {
    return L.divIcon({
        className: '',
        html: `<div style="
            background:${color};
            width:36px;height:36px;
            border-radius:50% 50% 50% 0;
            transform:rotate(-45deg);
            border:3px solid white;
            box-shadow:0 2px 8px rgba(0,0,0,0.3);
            display:flex;align-items:center;justify-content:center;
        "><span style="transform:rotate(45deg);font-size:16px;">${icon}</span></div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -36],
    });
}

// Cargar datos del servidor
function loadEmergencies(params = {}) {
    const url = new URL('{{ route("map.geojson") }}', window.location.origin);
    Object.entries(params).forEach(([k, v]) => v && url.searchParams.set(k, v));

    fetch(url)
        .then(r => r.json())
        .then(data => {
            // Limpiar marcadores anteriores
            Object.values(markers).forEach(m => map.removeLayer(m));
            markers = {};

            allFeatures = data.features;
            document.getElementById('count-badge').textContent = allFeatures.length;

            // Actualizar lista lateral
            const list = document.getElementById('emergency-list');
            if (allFeatures.length === 0) {
                list.innerHTML = '<div class="p-6 text-center text-sm text-gray-400">Sin emergencias activas</div>';
                return;
            }

            list.innerHTML = allFeatures.map(f => `
                <a href="${f.properties.url}" class="flex items-start gap-2 px-3 py-2.5 hover:bg-gray-50 transition cursor-pointer">
                    <span class="text-xl">${f.properties.type_icon}</span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-900">${f.properties.folio}</p>
                        <p class="text-xs text-gray-500 truncate">${f.properties.address}</p>
                        <p class="text-xs text-gray-400">${f.properties.created_at}</p>
                    </div>
                </a>
            `).join('');

            // Agregar marcadores
            allFeatures.forEach(f => {
                const [lng, lat] = f.geometry.coordinates;
                const p = f.properties;
                const marker = L.marker([lat, lng], {
                    icon: createIcon(p.color, p.type_icon)
                }).addTo(map);

                marker.bindPopup(`
                    <div class="emergency-popup">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                            <span style="font-size:1.5rem">${p.type_icon}</span>
                            <div>
                                <strong style="font-size:13px">${p.folio}</strong><br>
                                <span style="font-size:11px;color:#6b7280">${p.type_label}</span>
                            </div>
                        </div>
                        <p style="font-size:12px;color:#374151;margin-bottom:6px">${p.address}</p>
                        <div style="display:flex;gap:6px;margin-bottom:8px">
                            <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:999px;font-size:11px">${p.status_label}</span>
                        </div>
                        ${p.team ? `<p style="font-size:11px;color:#6b7280">👥 ${p.team}</p>` : ''}
                        <p style="font-size:11px;color:#9ca3af">${p.created_at}</p>
                        <a href="${p.url}" style="display:block;margin-top:8px;background:#2563eb;color:white;text-align:center;padding:6px;border-radius:6px;font-size:12px;text-decoration:none">Ver detalles →</a>
                    </div>
                `);

                markers[f.properties.id] = marker;
            });
        });
}

// Filtros
document.getElementById('apply-filter').addEventListener('click', () => {
    loadEmergencies({
        status: document.getElementById('filter-status').value,
        priority: document.getElementById('filter-priority').value,
    });
});

// Carga inicial
loadEmergencies();

// Refrescar cada 30 segundos
setInterval(() => loadEmergencies({
    status: document.getElementById('filter-status').value,
    priority: document.getElementById('filter-priority').value,
}), 30000);
</script>
@endpush
