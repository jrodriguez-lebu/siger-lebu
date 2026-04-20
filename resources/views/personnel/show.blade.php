@extends('layouts.app')
@section('title', $personnel->name)
@section('page-title', $personnel->name)
@section('page-subtitle', $personnel->getSpecialtyLabel() . ($personnel->position ? ' — ' . $personnel->position : ''))

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="badge {{ $personnel->getSpecialtyColor() }}">{{ $personnel->getSpecialtyLabel() }}</span>
            @if(!$personnel->is_active)
                <span class="badge badge-gray">Inactivo</span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('personnel.edit', $personnel->id) }}" class="btn-secondary">Editar</a>
            <a href="{{ route('personnel.index') }}" class="btn-outline">← Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Datos personales --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Información Personal</h3></div>
            <div class="card-body space-y-3">
                @php
                $rows = [
                    'RUT'             => $personnel->rut ?? '—',
                    'Cargo / Función' => $personnel->position ?? '—',
                    'Equipo'          => $personnel->team?->name ?? '—',
                    'Ingresó'         => $personnel->joined_date?->format('d/m/Y') ?? '—',
                ];
                @endphp
                @foreach($rows as $label => $value)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">{{ $label }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Contacto --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Contacto</h3></div>
            <div class="card-body space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Teléfono</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($personnel->phone)
                            <a href="tel:{{ $personnel->phone }}" class="text-blue-600 hover:underline">{{ $personnel->phone }}</a>
                        @else —
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($personnel->email)
                            <a href="mailto:{{ $personnel->email }}" class="text-blue-600 hover:underline">{{ $personnel->email }}</a>
                        @else —
                        @endif
                    </span>
                </div>
                @if($personnel->emergency_contact_name || $personnel->emergency_contact_phone)
                <div class="border-t border-gray-100 pt-3 mt-1">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Contacto de emergencia</p>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Nombre</span>
                        <span class="text-sm text-gray-900">{{ $personnel->emergency_contact_name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-sm text-gray-500">Teléfono</span>
                        <span class="text-sm text-gray-900">
                            @if($personnel->emergency_contact_phone)
                                <a href="tel:{{ $personnel->emergency_contact_phone }}" class="text-blue-600 hover:underline">{{ $personnel->emergency_contact_phone }}</a>
                            @else —
                            @endif
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>

    @if($personnel->notes)
    <div class="card">
        <div class="card-body">
            <p class="text-sm text-gray-500 mb-1">Notas</p>
            <p class="text-sm text-gray-700">{{ $personnel->notes }}</p>
        </div>
    </div>
    @endif

    <div class="flex items-center gap-3">
        <form method="POST" action="{{ route('personnel.destroy', $personnel->id) }}"
              onsubmit="return confirm('¿Eliminar a {{ addslashes($personnel->name) }}?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger">Eliminar</button>
        </form>
    </div>

</div>
@endsection
