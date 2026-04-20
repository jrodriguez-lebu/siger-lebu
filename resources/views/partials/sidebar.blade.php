<aside x-data="{ open: false }"
       :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white flex flex-col transition-transform duration-300 lg:static lg:translate-x-0">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-700">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-600 text-white font-bold text-sm">
            SG
        </div>
        <div>
            <p class="text-sm font-bold leading-tight">SIGER</p>
            <p class="text-xs text-gray-400 leading-tight">Municipalidad de Lebu</p>
        </div>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM13 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4a2 2 0 01-2-2V7zM3 17a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2zM13 17a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2h-4a2 2 0 01-2-2v-2z"/>
            </svg>
            Dashboard
        </a>

        {{-- Emergencias --}}
        <a href="{{ route('emergencies.index') }}"
           class="sidebar-link {{ request()->routeIs('emergencies.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Emergencias
        </a>

        {{-- Mapa --}}
        <a href="{{ route('map.index') }}"
           class="sidebar-link {{ request()->routeIs('map.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Mapa Operativo
        </a>

        @if(in_array(auth()->user()->role, ['admin', 'coordinador', 'lider']))
            {{-- Separator --}}
            <div class="pt-2 pb-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 px-2">Recursos</p>
            </div>

            {{-- Equipos --}}
            <a href="{{ route('teams.index') }}"
               class="sidebar-link {{ request()->routeIs('teams.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Equipos
            </a>

            {{-- Vehículos --}}
            <a href="{{ route('vehicles.index') }}"
               class="sidebar-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 17a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2zM3 10h18M5 10V7a2 2 0 012-2h10a2 2 0 012 2v3M3 10l1.5 6h15L21 10"/>
                </svg>
                Vehículos
            </a>

            {{-- Equipamiento --}}
            <a href="{{ route('equipment.index') }}"
               class="sidebar-link {{ request()->routeIs('equipment.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Equipamiento
            </a>

            {{-- Insumos --}}
            <a href="{{ route('supplies.index') }}"
               class="sidebar-link {{ request()->routeIs('supplies.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Insumos
            </a>

            {{-- Personal --}}
            <a href="{{ route('personnel.index') }}"
               class="sidebar-link {{ request()->routeIs('personnel.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Personal
            </a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'coordinador']))
            {{-- Separator --}}
            <div class="pt-2 pb-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 px-2">Análisis</p>
            </div>

            {{-- Reportes --}}
            <a href="{{ route('reports.index') }}"
               class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reportes
            </a>
        @endif

        @if(auth()->user()->role === 'admin')
            {{-- Separator --}}
            <div class="pt-2 pb-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 px-2">Administración</p>
            </div>

            {{-- Usuarios --}}
            <a href="{{ route('users.index') }}"
               class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Usuarios
            </a>

            {{-- WhatsApp --}}
            <a href="{{ route('whatsapp.index') }}"
               class="sidebar-link {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                WhatsApp
            </a>
        @endif

    </nav>

    {{-- Footer del sidebar --}}
    <div class="border-t border-gray-700 px-4 py-3">
        <div class="flex items-center gap-3">
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                 class="h-8 w-8 rounded-full object-cover">
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-gray-400">{{ auth()->user()->getRoleLabel() }}</p>
            </div>
        </div>
    </div>
</aside>
