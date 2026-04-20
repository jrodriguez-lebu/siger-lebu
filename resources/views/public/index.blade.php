@extends('layouts.guest')
@section('title', 'Portal de Emergencias')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-950 to-gray-900 text-white flex flex-col">

    {{-- Header --}}
    <header class="px-6 py-5 flex items-center justify-between max-w-6xl mx-auto w-full">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-600 font-bold text-sm">SG</div>
            <div>
                <p class="font-bold leading-tight">SIGER</p>
                <p class="text-blue-300 text-xs">Municipalidad de Lebu</p>
            </div>
        </div>
        <a href="{{ route('login') }}" class="text-sm text-blue-300 hover:text-white transition font-medium">
            Portal Interno →
        </a>
    </header>

    {{-- Hero --}}
    <main class="flex-1 flex items-center justify-center px-6 py-16">
        <div class="max-w-2xl text-center space-y-8">
            <div class="inline-flex items-center gap-2 rounded-full bg-red-600/20 border border-red-500/30 px-4 py-1.5 text-sm text-red-300">
                <span class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                Sistema activo 24/7
            </div>

            <h1 class="text-4xl sm:text-5xl font-bold leading-tight">
                Sistema de Gestión de<br>
                <span class="text-red-400">Emergencias</span>
            </h1>

            <p class="text-gray-300 text-lg leading-relaxed max-w-xl mx-auto">
                Reporta emergencias de forma rápida y segura. La Unidad de Gestión de Riesgo de Desastres de Lebu responderá a la brevedad.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('public.report') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-8 py-4 text-base font-semibold text-white shadow-lg hover:bg-red-700 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Reportar Emergencia
                </a>
                <a href="{{ route('public.map') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-8 py-4 text-base font-semibold text-white hover:bg-white/20 transition">
                    🗺️ Ver Mapa en Vivo
                </a>
            </div>

            {{-- Números de emergencia --}}
            <div class="grid grid-cols-3 gap-4 pt-4">
                @foreach([['133', 'Carabineros', 'blue'], ['132', 'Bomberos', 'red'], ['131', 'SAMU', 'green']] as [$num, $label, $color])
                <a href="tel:{{ $num }}" class="rounded-xl bg-white/5 border border-white/10 p-4 hover:bg-white/10 transition">
                    <p class="text-2xl font-bold text-{{ $color }}-400">{{ $num }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ $label }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </main>

    <footer class="text-center py-6 text-xs text-gray-500">
        © {{ date('Y') }} SIGER — Municipalidad de Lebu, Región del Biobío
    </footer>
</div>
@endsection
