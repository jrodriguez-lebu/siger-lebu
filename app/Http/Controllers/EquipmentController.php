<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Equipment::with('team')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('team_id'), fn ($q) => $q->where('team_id', $request->team_id))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($sq) use ($request) {
                $sq->where('name', 'like', "%{$request->search}%")
                   ->orWhere('code', 'like', "%{$request->search}%")
                   ->orWhere('serial_number', 'like', "%{$request->search}%");
            }))
            ->orderBy('name');

        $equipment = $query->paginate(15)->withQueryString();
        $teams     = Team::active()->orderBy('name')->get(['id', 'name']);

        return view('equipment.index', compact('equipment', 'teams'));
    }

    public function create(): View
    {
        $teams = Team::active()->orderBy('name')->get(['id', 'name']);
        return view('equipment.create', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'code'             => ['nullable', 'string', 'max:100', 'unique:equipment,code'],
            'category'         => ['required', 'in:herramienta,equipo_medico,equipo_rescate,comunicacion,proteccion,otro'],
            'description'      => ['nullable', 'string'],
            'brand'            => ['nullable', 'string', 'max:100'],
            'model'            => ['nullable', 'string', 'max:100'],
            'serial_number'    => ['nullable', 'string', 'max:100'],
            'status'           => ['required', 'in:disponible,en_uso,mantenimiento,dado_de_baja'],
            'team_id'          => ['nullable', 'exists:teams,id'],
            'purchase_date'    => ['nullable', 'date'],
            'last_maintenance' => ['nullable', 'date'],
            'next_maintenance' => ['nullable', 'date'],
            'notes'            => ['nullable', 'string'],
        ]);

        $item = Equipment::create($validated);

        return redirect()->route('equipment.index')
            ->with('success', "Equipo \"{$item->name}\" creado correctamente.");
    }

    public function show(Equipment $equipment): View
    {
        $equipment->load('team');
        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment): View
    {
        $teams = Team::active()->orderBy('name')->get(['id', 'name']);
        return view('equipment.edit', compact('equipment', 'teams'));
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'code'             => ['nullable', 'string', 'max:100', "unique:equipment,code,{$equipment->id}"],
            'category'         => ['required', 'in:herramienta,equipo_medico,equipo_rescate,comunicacion,proteccion,otro'],
            'description'      => ['nullable', 'string'],
            'brand'            => ['nullable', 'string', 'max:100'],
            'model'            => ['nullable', 'string', 'max:100'],
            'serial_number'    => ['nullable', 'string', 'max:100'],
            'status'           => ['required', 'in:disponible,en_uso,mantenimiento,dado_de_baja'],
            'team_id'          => ['nullable', 'exists:teams,id'],
            'purchase_date'    => ['nullable', 'date'],
            'last_maintenance' => ['nullable', 'date'],
            'next_maintenance' => ['nullable', 'date'],
            'notes'            => ['nullable', 'string'],
        ]);

        $equipment->update($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        $equipment->delete();
        return redirect()->route('equipment.index')
            ->with('success', "Equipo \"{$equipment->name}\" eliminado.");
    }
}
