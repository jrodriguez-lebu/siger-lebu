@extends('layouts.guest')
@section('title', 'Reporte Recibido')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="flex justify-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">¡Reporte Recibido!</h1>
            <p class="text-gray-500 mt-2">Tu reporte de emergencia ha sido ingresado al sistema y será atendido a la brevedad.</p>
        </div>
        @if($folio)
        <div class="rounded-xl bg-blue-50 border border-blue-200 p-4">
            <p class="text-sm text-blue-700">Número de folio</p>
            <p class="text-2xl font-bold text-blue-900 mt-1 tracking-wider">{{ $folio }}</p>
            <p class="text-xs text-blue-600 mt-1">Guarda este número para hacer seguimiento de tu reporte</p>
        </div>
        @endif
        <div class="space-y-3 text-sm text-gray-600">
            <p>📞 Si la situación empeora, llama al:</p>
            <div class="flex justify-center gap-4">
                <span class="font-bold text-red-600">133 Carabineros</span>
                <span class="font-bold text-orange-600">132 Bomberos</span>
                <span class="font-bold text-blue-600">131 SAMU</span>
            </div>
        </div>
        <a href="{{ route('public.report') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            Reportar otra emergencia
        </a>
    </div>
</div>
@endsection
