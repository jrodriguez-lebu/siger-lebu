<?php

namespace App\Http\Controllers;

use App\Models\EmergencySupply;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplyController extends Controller
{
    public function index(Request $request): View
    {
        $query = EmergencySupply::with('team')
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('team_id'), fn ($q) => $q->where('team_id', $request->team_id))
            ->when($request->boolean('low_stock'), fn ($q) => $q->belowMinimum())
            ->when($request->filled('search'), fn ($q) => $q->where(function ($sq) use ($request) {
                $sq->where('name', 'like', "%{$request->search}%")
                   ->orWhere('code', 'like', "%{$request->search}%");
            }))
            ->orderByRaw('stock_current <= stock_minimum DESC')
            ->orderBy('name');

        $supplies = $query->paginate(15)->withQueryString();
        $teams    = Team::active()->orderBy('name')->get(['id', 'name']);

        return view('supplies.index', compact('supplies', 'teams'));
    }

    public function create(): View
    {
        $teams = Team::active()->orderBy('name')->get(['id', 'name']);
        return view('supplies.create', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'code'          => ['nullable', 'string', 'max:100', 'unique:emergency_supplies,code'],
            'category'      => ['required', 'in:medicamento,material_curacion,oxigeno,combustible,alimento,ropa,herramienta,otro'],
            'unit'          => ['required', 'string', 'max:50'],
            'stock_current' => ['required', 'numeric', 'min:0'],
            'stock_minimum' => ['required', 'numeric', 'min:0'],
            'stock_maximum' => ['nullable', 'numeric', 'min:0'],
            'team_id'       => ['nullable', 'exists:teams,id'],
            'location'      => ['nullable', 'string', 'max:200'],
            'expiry_date'   => ['nullable', 'date'],
            'supplier'      => ['nullable', 'string', 'max:200'],
            'unit_cost'     => ['nullable', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'string'],
        ]);

        $supply = EmergencySupply::create($validated);

        return redirect()->route('supplies.index')
            ->with('success', "Insumo \"{$supply->name}\" creado correctamente.");
    }

    public function show(EmergencySupply $supply): View
    {
        $supply->load('team');
        return view('supplies.show', compact('supply'));
    }

    public function edit(EmergencySupply $supply): View
    {
        $teams = Team::active()->orderBy('name')->get(['id', 'name']);
        return view('supplies.edit', compact('supply', 'teams'));
    }

    public function update(Request $request, EmergencySupply $supply): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'code'          => ['nullable', 'string', 'max:100', "unique:emergency_supplies,code,{$supply->id}"],
            'category'      => ['required', 'in:medicamento,material_curacion,oxigeno,combustible,alimento,ropa,herramienta,otro'],
            'unit'          => ['required', 'string', 'max:50'],
            'stock_current' => ['required', 'numeric', 'min:0'],
            'stock_minimum' => ['required', 'numeric', 'min:0'],
            'stock_maximum' => ['nullable', 'numeric', 'min:0'],
            'team_id'       => ['nullable', 'exists:teams,id'],
            'location'      => ['nullable', 'string', 'max:200'],
            'expiry_date'   => ['nullable', 'date'],
            'supplier'      => ['nullable', 'string', 'max:200'],
            'unit_cost'     => ['nullable', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'string'],
        ]);

        $supply->update($validated);

        return redirect()->route('supplies.index')
            ->with('success', 'Insumo actualizado correctamente.');
    }

    public function destroy(EmergencySupply $supply): RedirectResponse
    {
        $supply->delete();
        return redirect()->route('supplies.index')
            ->with('success', "Insumo \"{$supply->name}\" eliminado.");
    }
}
