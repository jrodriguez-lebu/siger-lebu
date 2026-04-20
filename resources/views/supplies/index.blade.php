@extends('layouts.app')
@section('title', 'Insumos')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Insumos y Suministros</h1>
            <p class="text-sm text-gray-500">Control de inventario de insumos de emergencia</p>
        </div>
        <a href="{{ route('supplies.create') }}" class="btn-primary">+ Nuevo Insumo</a>
    </div>

    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('supplies.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nombre, código...">
                </div>
                <div>
                    <label class="form-label">Categoría</label>
                    <select name="category" class="form-select">
                        <option value="">Todas</option>
                        @foreach(['medicamento' => 'Medicamento','material_curacion' => 'Material de Curación','oxigeno' => 'Oxígeno','combustible' => 'Combustible','alimento' => 'Alimento','ropa' => 'Ropa/Vestuario','herramienta' => 'Herramienta','otro' => 'Otro'] as $val => $label)
                            <option value="{{ $val }}" {{ request('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Equipo</label>
                    <select name="team_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600">
                        <span class="text-sm text-gray-700">Solo stock bajo</span>
                    </label>
                </div>
                <button type="submit" class="btn-primary">Filtrar</button>
                <a href="{{ route('supplies.index') }}" class="btn-secondary">Limpiar</a>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Categoría</th>
                        <th>Equipo</th>
                        <th>Stock actual</th>
                        <th>Nivel de stock</th>
                        <th>Vencimiento</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplies as $supply)
                    <tr class="{{ $supply->isLowStock() ? 'bg-red-50' : '' }}">
                        <td>
                            <div class="flex items-center gap-2">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $supply->name }}</p>
                                    @if($supply->code)
                                        <p class="text-xs font-mono text-gray-400">{{ $supply->code }}</p>
                                    @endif
                                </div>
                                @if($supply->isLowStock())
                                    <span class="badge badge-red text-xs">Stock Bajo</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-blue">{{ $supply->getCategoryLabel() }}</span>
                        </td>
                        <td>{{ $supply->team?->name ?? '—' }}</td>
                        <td>
                            <span class="font-semibold {{ $supply->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ number_format($supply->stock_current, 1) }}
                            </span>
                            <span class="text-xs text-gray-400">/ {{ number_format($supply->stock_minimum, 1) }} mín.</span>
                            <span class="text-xs text-gray-400 ml-1">{{ $supply->unit }}</span>
                        </td>
                        <td class="w-36">
                            @php $pct = min(100, $supply->getStockPercentage()); @endphp
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $supply->isLowStock() ? 'bg-red-500' : ($pct < 50 ? 'bg-yellow-400' : 'bg-green-500') }}"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-8">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td>
                            @if($supply->expiry_date)
                                @php $exp = $supply->expiry_date; @endphp
                                <span class="text-xs {{ $exp->isPast() ? 'text-red-600 font-semibold' : ($supply->isExpiringSoon() ? 'text-amber-600' : 'text-gray-600') }}">
                                    {{ $exp->format('d/m/Y') }}
                                    @if($exp->isPast()) (vencido) @elseif($supply->isExpiringSoon()) (pronto) @endif
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('supplies.edit', $supply->id) }}" class="btn-secondary text-sm">Editar</a>
                                <form method="POST" action="{{ route('supplies.destroy', $supply->id) }}"
                                      onsubmit="return confirm('¿Eliminar {{ addslashes($supply->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-10">No se encontraron insumos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($supplies->hasPages())
        <div class="card-body border-t border-gray-100">
            {{ $supplies->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
