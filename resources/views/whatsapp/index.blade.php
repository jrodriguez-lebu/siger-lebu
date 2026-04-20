@extends('layouts.app')
@section('title', 'WhatsApp — Alertas')

@section('content')
<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Alertas WhatsApp</h1>
            <p class="text-sm text-gray-500">Historial de notificaciones enviadas a líderes de equipo</p>
        </div>
    </div>

    {{-- Estado del servicio --}}
    <div class="card">
        <div class="card-body">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Configuración del Servicio</h2>
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Estado:</span>
                    @if($config['enabled'])
                        <span class="badge badge-green">Habilitado</span>
                    @else
                        <span class="badge badge-red">Deshabilitado</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Proveedor:</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700 uppercase">
                        {{ $config['provider'] ?? 'no configurado' }}
                    </span>
                </div>
                <div class="ml-auto text-xs text-gray-400">
                    Para cambiar la configuración, edite las variables de entorno
                    <code class="bg-gray-100 px-1 rounded">WHATSAPP_ENABLED</code> y
                    <code class="bg-gray-100 px-1 rounded">WHATSAPP_PROVIDER</code>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="card-body text-center">
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total enviados</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <p class="text-3xl font-bold text-green-600">{{ $stats['enviados'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Exitosos</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <p class="text-3xl font-bold text-red-600">{{ $stats['fallidos'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Fallidos</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pendientes'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Pendientes</p>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('whatsapp.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-input" placeholder="Nombre o teléfono...">
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="enviado"   {{ request('status') === 'enviado'   ? 'selected' : '' }}>Enviado</option>
                        <option value="fallido"   {{ request('status') === 'fallido'   ? 'selected' : '' }}>Fallido</option>
                        <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>
                <button type="submit" class="btn-secondary">Filtrar</button>
                @if(request()->hasAny(['search','status']))
                    <a href="{{ route('whatsapp.index') }}" class="btn-ghost">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Destinatario</th>
                        <th class="px-4 py-3 text-left">Teléfono</th>
                        <th class="px-4 py-3 text-left">Emergencia</th>
                        <th class="px-4 py-3 text-left">Proveedor</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-left">Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $log->recipient_name }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 font-mono text-xs">
                                {{ $log->phone }}
                            </td>
                            <td class="px-4 py-3">
                                @if($log->emergency)
                                    <a href="{{ route('emergencies.show', $log->emergency) }}"
                                       class="text-blue-600 hover:underline font-medium">
                                        {{ $log->emergency->folio }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 uppercase text-xs">
                                {{ $log->provider ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $log->getStatusColor() }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-red-600 max-w-xs truncate">
                                {{ $log->error ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                No hay registros de alertas WhatsApp aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
