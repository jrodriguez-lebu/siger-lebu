<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\Team;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $query = Team::withCount('teamMembers')
            ->with('leader')
            ->when($request->filled('name'), fn ($q) => $q->where('name', 'like', "%{$request->name}%"))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->orderBy('name');

        $teams = $query->paginate(15)->withQueryString();

        return view('teams.index', compact('teams'));
    }

    public function create(): View
    {
        $leaders = User::where('role', 'lider')->orWhere('role', 'coordinador')->orderBy('name')->get();
        return view('teams.create', compact('leaders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:rescate,tecnico,bombero,medico,apoyo'],
            'color'       => ['nullable', 'string', 'max:20'],
            'status'      => ['required', 'in:activo,inactivo,disuelto'],
            'leader_id'   => ['nullable', 'exists:users,id'],
        ]);

        $team = Team::create($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', "Equipo \"{$team->name}\" creado correctamente.");
    }

    public function show(Team $team): View
    {
        $team->load([
            'leader',
            'teamMembers.user',
            'personnel',
            'vehicles',
            'emergencies' => fn ($q) => $q->orderByDesc('created_at')->limit(10),
        ]);

        $availablePersonnel = Personnel::where(function ($query) use ($team) {
                $query->whereNull('team_id')
                    ->orWhere('team_id', '!=', $team->id);
            })
            ->orderBy('name')
            ->get();

        $availableVehicles = Vehicle::whereNull('team_id')
            ->whereNotIn('status', ['dado_de_baja'])
            ->orderBy('name')
            ->get();

        return view('teams.show', compact('team', 'availablePersonnel', 'availableVehicles'));
    }

    public function edit(Team $team): View
    {
        $leaders = User::where('role', 'lider')->orWhere('role', 'coordinador')->orderBy('name')->get();
        return view('teams.edit', compact('team', 'leaders'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:rescate,tecnico,bombero,medico,apoyo'],
            'color'       => ['nullable', 'string', 'max:20'],
            'status'      => ['required', 'in:activo,inactivo,disuelto'],
            'leader_id'   => ['nullable', 'exists:users,id'],
        ]);

        $team->update($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();
        return redirect()->route('teams.index')
            ->with('success', "Equipo \"{$team->name}\" eliminado.");
    }

    public function addMember(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'personnel_id' => ['required', 'exists:personnel,id'],
            'role_in_team' => ['required', 'string', 'max:100'],
        ]);

        $personnel = Personnel::findOrFail($validated['personnel_id']);

        if ($personnel->team_id === $team->id) {
            return back()->with('error', 'El personal ya está asignado a este equipo.');
        }

        $personnel->update([
            'team_id'   => $team->id,
            'position'  => $validated['role_in_team'],
        ]);

        return back()->with('success', 'Personal asignado correctamente al equipo.');
    }

    public function removeMember(Team $team, Personnel $personnel): RedirectResponse
    {
        if ($personnel->team_id === $team->id) {
            $personnel->update(['team_id' => null]);
        }

        return back()->with('success', "{$personnel->name} desasignado del equipo.");
    }

    public function addVehicle(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $vehicle->update(['team_id' => $team->id]);

        return back()->with('success', "Vehículo \"{$vehicle->name}\" asignado al equipo.");
    }

    public function removeVehicle(Team $team, Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->team_id === $team->id) {
            $vehicle->update(['team_id' => null]);
        }

        return back()->with('success', "\"{$vehicle->name}\" desasignado del equipo.");
    }

    public function active(): JsonResponse
    {
        $teams = Team::active()->orderBy('name')->get(['id', 'name']);
        return response()->json($teams);
    }
}
