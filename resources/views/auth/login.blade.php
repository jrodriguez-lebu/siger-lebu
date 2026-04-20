@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="min-h-screen flex">

    {{-- Panel izquierdo (decorativo) --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gray-900 via-blue-950 to-gray-900 flex-col justify-between p-12">
        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-600 text-white font-bold">SG</div>
                <div>
                    <p class="text-white font-bold text-lg leading-tight">SIGER</p>
                    <p class="text-blue-300 text-xs">Sistema de Gestión de Emergencias</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-3">
                @foreach([
                    ['🚨', 'Gestión en tiempo real', 'Monitorea y coordina emergencias desde cualquier dispositivo'],
                    ['🗺️', 'Mapa interactivo', 'Visualiza emergencias geolocalizadas en el mapa de Lebu'],
                    ['📊', 'Reportes y estadísticas', 'Genera reportes detallados para la toma de decisiones'],
                ] as [$icon, $title, $desc])
                <div class="flex items-start gap-4">
                    <span class="text-2xl">{{ $icon }}</span>
                    <div>
                        <p class="text-white font-semibold text-sm">{{ $title }}</p>
                        <p class="text-blue-300 text-xs leading-relaxed">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <p class="text-gray-500 text-xs">
                Municipalidad de Lebu — Unidad de Gestión de Riesgo de Desastres<br>
                © {{ date('Y') }} SIGER. Todos los derechos reservados.
            </p>
        </div>
    </div>

    {{-- Panel derecho (formulario) --}}
    <div class="flex flex-1 items-center justify-center px-6 py-12 lg:px-16">
        <div class="w-full max-w-md space-y-8">

            {{-- Logo móvil --}}
            <div class="lg:hidden flex items-center gap-3 justify-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-600 text-white font-bold">SG</div>
                <div>
                    <p class="font-bold text-lg text-gray-900">SIGER</p>
                    <p class="text-gray-500 text-xs">Sistema de Gestión de Emergencias</p>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900">Iniciar Sesión</h2>
                <p class="mt-1 text-sm text-gray-500">Accede al portal de gestión de emergencias</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           autocomplete="email" autofocus
                           class="form-input @error('email') border-red-500 @enderror"
                           placeholder="usuario@municipalidadlebu.cl">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" id="password" name="password"
                               autocomplete="current-password"
                               class="form-input pr-10 @error('password') border-red-500 @enderror"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600">
                        Recordarme
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Ingresar al sistema
                </button>
            </form>

            {{-- Link portal público --}}
            <div class="text-center">
                <a href="{{ route('public.report') }}"
                   class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    ¿Necesitas reportar una emergencia? → Portal Público
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
