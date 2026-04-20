@extends('layouts.app')
@section('title', 'Reportes')
@section('page-title', 'Reportes y Estadísticas')
@section('page-subtitle', 'Exporta y analiza la información del sistema')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    {{-- Reporte Emergencias --}}
    <div class="card hover:shadow-md transition">
        <div class="card-body text-center py-8 space-y-4">
            <div class="flex justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-100 text-3xl">🚨</div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-lg">Reporte de Emergencias</h3>
                <p class="text-sm text-gray-500 mt-1">Listado detallado con filtros por fecha, estado, tipo y equipo</p>
            </div>
            <div class="flex flex-col gap-2">
                <a href="{{ route('reports.emergencies') }}" class="btn-primary justify-center">
                    Ver Reporte
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('reports.exportPdf') }}" class="btn-danger flex-1 justify-center text-xs">
                        📄 PDF
                    </a>
                    <a href="{{ route('reports.exportExcel') }}" class="btn-success flex-1 justify-center text-xs">
                        📊 Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Reporte Inventario --}}
    <div class="card hover:shadow-md transition">
        <div class="card-body text-center py-8 space-y-4">
            <div class="flex justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-yellow-100 text-3xl">📦</div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-lg">Inventario de Insumos</h3>
                <p class="text-sm text-gray-500 mt-1">Stock actual, alertas de inventario mínimo y vencimientos</p>
            </div>
            <a href="{{ route('reports.inventory') }}" class="btn-primary justify-center">
                Ver Inventario
            </a>
        </div>
    </div>

    {{-- Estadísticas generales --}}
    <div class="card hover:shadow-md transition">
        <div class="card-body text-center py-8 space-y-4">
            <div class="flex justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-100 text-3xl">📊</div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-lg">Dashboard</h3>
                <p class="text-sm text-gray-500 mt-1">Estadísticas en tiempo real y gráficos operativos</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-primary justify-center">
                Ver Dashboard
            </a>
        </div>
    </div>

</div>
@endsection
