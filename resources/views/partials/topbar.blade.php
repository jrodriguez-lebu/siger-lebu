<header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">

    {{-- Título de página --}}
    <div>
        <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
        @hasSection('page-subtitle')
            <p class="text-sm text-gray-500">@yield('page-subtitle')</p>
        @endif
    </div>

    {{-- Acciones del header --}}
    <div class="flex items-center gap-4">

        {{-- Botón Nueva Emergencia (acceso rápido) --}}
        @if(in_array(auth()->user()->role, ['admin', 'coordinador', 'digitador']))
            <a href="{{ route('emergencies.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Emergencia
            </a>
        @endif

        {{-- Menú usuario --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                     class="h-7 w-7 rounded-full object-cover">
                <span class="hidden sm:block font-medium">{{ auth()->user()->name }}</span>
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-100 bg-white py-1 shadow-lg z-50">
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ auth()->user()->getRoleLabel() }}</p>
                </div>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Mi Perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>
