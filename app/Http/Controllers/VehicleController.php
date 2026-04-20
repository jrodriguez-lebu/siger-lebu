<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vehicle::with('team')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('team_id'), fn ($q) => $q->where('team_id', $request->team_id))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($sq) use ($request) {
                $sq->where('name', 'like', "%{$request->search}%")
                   ->orWhere('plate', 'like', "%{$request->search}%")
                   ->orWhere('brand', 'like', "%{$request->search}%");
            }))
            ->orderBy('name');

        $vehicles = $query->paginate(15)->withQueryString();
        $teams    = Team::active()->orderBy('name')->get(['id', 'name']);

        return view('vehicles.index', compact('vehicles', 'teams'));
    }

    public function create(): View
    {
        $teams = Team::active()->orderBy('name')->get(['id', 'name']);
        return view('vehicles.create', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'plate'                   => ['required', 'string', 'max:20', 'unique:vehicles,plate'],
            'type'                    => ['required', 'in:ambulancia,camion_bomberos,camioneta,furgon,moto,helicoptero,bote,otro'],
            'brand'                   => ['nullable', 'string', 'max:100'],
            'model'                   => ['nullable', 'string', 'max:100'],
            'year'                    => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color'                   => ['nullable', 'string', 'max:50'],
            'capacity'                => ['nullable', 'integer', 'min:1'],
            'status'                  => ['required', 'in:disponible,en_servicio,mantenimiento,fuera_de_servicio'],
            'team_id'                 => ['nullable', 'exists:teams,id'],
            'fuel_type'               => ['nullable', 'string', 'max:50'],
            'current_mileage'         => ['nullable', 'integer', 'min:0'],
            'last_service_date'       => ['nullable', 'date'],
            'next_service_date'       => ['nullable', 'date'],
            'insurance_expiry'        => ['nullable', 'date'],
            'technical_review_expiry' => ['nullable', 'date'],
            'gps_tracking_id'         => ['nullable', 'string', 'max:100'],
            'notes'                   => ['nullable', 'string'],
        ]);

        $vehicle = Vehicle::create($validated);

        return redirect()->route('vehicles.show', $vehicle->id)
            ->with('success', "Vehículo \"{$vehicle->name}\" creado correctamente.");
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['team', 'emergencies' => fn ($q) => $q->orderByDesc('created_at')->limit(10)]);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle): View
    {
        $teams = Team::active()->orderBy('name')->get(['id', 'name']);
        return view('vehicles.edit', compact('vehicle', 'teams'));
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'plate'                   => ['required', 'string', 'max:20', "unique:vehicles,plate,{$vehicle->id}"],
            'type'                    => ['required', 'in:ambulancia,camion_bomberos,camioneta,furgon,moto,helicoptero,bote,otro'],
            'brand'                   => ['nullable', 'string', 'max:100'],
            'model'                   => ['nullable', 'string', 'max:100'],
            'year'                    => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color'                   => ['nullable', 'string', 'max:50'],
            'capacity'                => ['nullable', 'integer', 'min:1'],
            'status'                  => ['required', 'in:disponible,en_servicio,mantenimiento,fuera_de_servicio'],
            'team_id'                 => ['nullable', 'exists:teams,id'],
            'fuel_type'               => ['nullable', 'string', 'max:50'],
            'current_mileage'         => ['nullable', 'integer', 'min:0'],
            'last_service_date'       => ['nullable', 'date'],
            'next_service_date'       => ['nullable', 'date'],
            'insurance_expiry'        => ['nullable', 'date'],
            'technical_review_expiry' => ['nullable', 'date'],
            'gps_tracking_id'         => ['nullable', 'string', 'max:100'],
            'notes'                   => ['nullable', 'string'],
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle->id)
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')
            ->with('success', "Vehículo \"{$vehicle->name}\" eliminado.");
    }

    public function available(): JsonResponse
    {
        $vehicles = Vehicle::available()->orderBy('name')->get(['id', 'name', 'plate', 'type']);
        return response()->json($vehicles);
    }
}
