@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen operativo — ' . now()->isoFormat('dddd D [de] MMMM, YYYY'))

@section('content')

{{-- ── Estadísticas clave ─────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

    @php
    $statCards = [
        ['label' => 'Emergencias Activas', 'value' => $stats['total_active'],       'icon' => '🚨', 'color' => 'red',    'route' => 'emergencies.index'],
        ['label' => 'Hoy',                 'value' => $stats['total_today'],        'icon' => '📅', 'color' => 'blue',   'route' => 'emergencies.index'],
        ['label' => 'Este Mes',            'value' => $stats['total_month'],        'icon' => '📊', 'color' => 'purple', 'route' => 'emergencies.index'],
        ['label' => 'Equipos Activos',     'value' => $stats['teams_active'],       'icon' => '👥', 'color' => 'green',  'route' => 'teams.index'],
        ['label' => 'Vehículos Disponibles','value' => $stats['vehicles_available'], 'icon' => '🚐', 'color' => 'teal',   'route' => 'vehicles.index'],
        ['label' => 'Stock Crítico',        'value' => $stats['low_stock'],          'icon' => '⚠️', 'color' => 'yellow', 'route' => 'supplies.index'],
    ];
    @endphp

    @foreach($statCards as $card)
    <a href="{{ route($card['route']) }}" class="card p-4 hover:shadow-md transition group">
        <div class="flex items-center justify-between mb-2">
            <span class="text-2xl">{{ $card['icon'] }}</span>
            <svg class="h-4 w-4 text-gray-300 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</p>
    </a>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Emergencias recientes ─────────────────────────── --}}
    <div class="xl:col-span-2 card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Emergencias Recientes</h3>
            <a href="{{ route('emergencies.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Ver todas →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentEmergencies as $emergency)
            <a href="{{ route('emergencies.show', $emergency) }}"
               class="flex items-start gap-3 px-6 py-3 hover:bg-gray-50 transition">
                <span class="text-xl mt-0.5">{{ $emergency->getTypeIcon() }}</span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $emergency->folio }}</p>
                        <span class="badge badge-{{ $emergency->getStatusColor() }}">{{ $emergency->getStatusLabel() }}</span>
                        <span class="badge badge-{{ $emergency->getPriorityColor() === 'orange' ? 'yellow' : $emergency->getPriorityColor() }}">
                            {{ $emergency->getPriorityLabel() }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $emergency->address }}</p>
                    @if($emergency->assignedTeam)
                    <p class="text-xs text-blue-600 mt-0.5">{{ $emergency->assignedTeam->name }}</p>
                    @endif
                </div>
                <p class="text-xs text-gray-400 flex-shrink-0">{{ $emergency->created_at->diffForHumans() }}</p>
            </a>
            @empty
            <div class="px-6 py-10 text-center">
                <p class="text-4xl mb-2">✅</p>
                <p class="text-sm text-gray-500">Sin emergencias activas en este momento</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Panel lateral ─────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Distribución por estado --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 text-sm">Estado de Emergencias</h3>
            </div>
            <div class="card-body space-y-2">
                @php
                $statusConfig = [
                    'ingresada'  => ['label' => 'Ingresadas', 'color' => 'bg-blue-500'],
                    'en_proceso' => ['label' => 'En Proceso', 'color' => 'bg-yellow-500'],
                    'atendida'   => ['label' => 'Atendidas',  'color' => 'bg-green-500'],
                    'cerrada'    => ['label' => 'Cerradas',   'color' => 'bg-gray-400'],
                    'cancelada'  => ['label' => 'Canceladas', 'color' => 'bg-red-400'],
                ];
                $total = $byStatus->sum();
                @endphp
                @foreach($statusConfig as $key => $config)
                @php $count = $byStatus->get($key, 0); $pct = $total > 0 ? round(($count / $total) * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>{{ $config['label'] }}</span>
                        <span>{{ $count }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="{{ $config['color'] }} h-full rounded-full transition-all"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stock crítico --}}
        @if($criticalSupplies->isNotEmpty())
        <div class="card border-yellow-200">
            <div class="card-header bg-yellow-50">
                <h3 class="font-semibold text-yellow-800 text-sm flex items-center gap-2">
                    ⚠️ Stock Crítico
                </h3>
                <a href="{{ route('supplies.index') }}" class="text-xs text-yellow-600 hover:text-yellow-700">Ver →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($criticalSupplies as $supply)
                <div class="px-4 py-2.5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $supply->name }}</p>
                        <p class="text-xs text-gray-500">{{ $supply->team?->name ?? 'Sin equipo' }}</p>
                    </div>
                    <span class="badge badge-red">{{ $supply->stock_current }} {{ $supply->unit }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Acciones rápidas --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 text-sm">Acciones Rápidas</h3>
            </div>
            <div class="card-body grid grid-cols-2 gap-2">
                @if(in_array(auth()->user()->role, ['admin', 'coordinador', 'digitador']))
                <a href="{{ route('emergencies.create') }}" class="btn-danger text-xs justify-center py-2">
                    🚨 Nueva Emergencia
                </a>
                @endif
                <a href="{{ route('map.index') }}" class="btn-secondary text-xs justify-center py-2">
                    🗺️ Ver Mapa
                </a>
                @if(in_array(auth()->user()->role, ['admin', 'coordinador']))
                <a href="{{ route('reports.index') }}" class="btn-outline text-xs justify-center py-2">
                    📊 Reportes
                </a>
                @endif
                <a href="{{ route('teams.index') }}" class="btn-outline text-xs justify-center py-2">
                    👥 Equipos
                </a>
            </div>
        </div>

    </div>
</div>

@endsection

@php
$typeLabelsMap = ['incendio'=>'🔥 Incendio','accidente_transito'=>'🚗 Accidente','rescate'=>'🆘 Rescate','inundacion'=>'🌊 Inundación','emergencia_medica'=>'🏥 Médica','derrumbe'=>'⛰️ Derrumbe','otro'=>'⚠️ Otro'];
$chartLabels = $byType->keys()->map(fn($k) => $typeLabelsMap[$k] ?? $k)->values();
$chartValues = $byType->values();
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de emergencias por tipo
const ctx = document.getElementById('typeChart');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                data: @json($chartValues),
                backgroundColor: ['#ef4444','#f97316','#eab308','#3b82f6','#10b981','#8b5cf6','#6b7280'],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
        }
    });
}
</script>
@endpush
