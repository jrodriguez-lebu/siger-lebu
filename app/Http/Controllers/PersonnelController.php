<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonnelController extends Controller
{
    public function index(Request $request): View
    {
        $query = Personnel::with('team')
            ->when($request->filled('team_id'),   fn ($q) => $q->where('team_id', $request->team_id))
            ->when($request->filled('specialty'), fn ($q) => $q->where('specialty', $request->specialty))
            ->when($request->filled('status'),    fn ($q) => $q->where('is_active', $request->status === 'activo'))
            ->when($request->filled('search'),    fn ($q) => $q->where(function ($sq) use ($request) {
                $sq->where('name', 'like', "%{$request->search}%")
                   ->orWhere('rut', 'like', "%{$request->search}%")
                   ->orWhere('position', 'like', "%{$request->search}%");
            }))
            ->orderBy('name');

        $personnel = $query->paginate(20)->withQueryString();
        $teams     = Team::orderBy('name')->get(['id', 'name']);

        return view('personnel.index', compact('personnel', 'teams'));
    }

    public function create(Request $request): View
    {
        $teams         = Team::orderBy('name')->get(['id', 'name']);
        $defaultTeamId = $request->query('team_id');
        return view('personnel.create', compact('teams', 'defaultTeamId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:150'],
            'rut'                     => ['nullable', 'string', 'max:20', 'unique:personnel,rut'],
            'specialty'               => ['required', 'in:bombero,paramedico,enfermero,medico,rescatista,logistica,comunicaciones,carabinero,voluntario,otro'],
            'position'                => ['nullable', 'string', 'max:100'],
            'phone'                   => ['nullable', 'string', 'max:20'],
            'email'                   => ['nullable', 'email', 'max:150'],
            'team_id'                 => ['nullable', 'exists:teams,id'],
            'is_active'               => ['boolean'],
            'joined_date'             => ['nullable', 'date'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'notes'                   => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $person = Personnel::create($validated);

        return redirect()->route('personnel.show', $person->id)
            ->with('success', "Personal \"{$person->name}\" registrado correctamente.");
    }

    public function show(Personnel $personnel): View
    {
        $personnel->load('team');
        return view('personnel.show', compact('personnel'));
    }

    public function edit(Personnel $personnel): View
    {
        $teams = Team::orderBy('name')->get(['id', 'name']);
        return view('personnel.edit', compact('personnel', 'teams'));
    }

    public function update(Request $request, Personnel $personnel): RedirectResponse
    {
        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:150'],
            'rut'                     => ['nullable', 'string', 'max:20', "unique:personnel,rut,{$personnel->id}"],
            'specialty'               => ['required', 'in:bombero,paramedico,enfermero,medico,rescatista,logistica,comunicaciones,carabinero,voluntario,otro'],
            'position'                => ['nullable', 'string', 'max:100'],
            'phone'                   => ['nullable', 'string', 'max:20'],
            'email'                   => ['nullable', 'email', 'max:150'],
            'team_id'                 => ['nullable', 'exists:teams,id'],
            'is_active'               => ['boolean'],
            'joined_date'             => ['nullable', 'date'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'notes'                   => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $personnel->update($validated);

        return redirect()->route('personnel.show', $personnel->id)
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(Personnel $personnel): RedirectResponse
    {
        $name = $personnel->name;
        $personnel->delete();
        return redirect()->route('personnel.index')
            ->with('success', "\"{$name}\" eliminado del registro.");
    }
}
