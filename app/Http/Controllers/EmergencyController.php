<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsAppAlert;
use App\Models\Emergency;
use App\Models\EmergencyPhoto;
use App\Models\Team;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmergencyController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = Emergency::with(['assignedTeam', 'createdBy', 'photos'])
            ->when($user->role === 'lider', function ($q) use ($user) {
                // Líderes solo ven las asignadas a su equipo
                $q->where('assigned_team_id', function ($sq) use ($user) {
                    $sq->select('team_id')->from('team_members')->where('user_id', $user->id)->limit(1);
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->priority))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($sq) use ($request) {
                $sq->where('folio', 'like', "%{$request->search}%")
                   ->orWhere('title', 'like', "%{$request->search}%")
                   ->orWhere('address', 'like', "%{$request->search}%")
                   ->orWhere('reported_by_name', 'like', "%{$request->search}%");
            }))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByRaw("FIELD(priority, 'critica', 'alta', 'media', 'baja')")
            ->orderByDesc('created_at');

        $emergencies = $query->paginate(20)->withQueryString();

        return view('emergencies.index', compact('emergencies'));
    }

    public function create(): View
    {
        $teams = Team::active()->with('leader')->get();
        return view('emergencies.create', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'              => ['required', 'in:incendio,accidente_transito,rescate,inundacion,emergencia_medica,derrumbe,otro'],
            'priority'          => ['required', 'in:baja,media,alta,critica'],
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'address'           => ['required', 'string', 'max:500'],
            'sector'            => ['nullable', 'string', 'max:200'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            'reported_by_name'  => ['nullable', 'string', 'max:200'],
            'reported_by_phone' => ['nullable', 'string', 'max:50'],
            'affected_people'   => ['required', 'integer', 'min:0'],
            'assigned_team_id'  => ['nullable', 'exists:teams,id'],
            'notes'             => ['nullable', 'string'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status']     = 'ingresada';

        $emergency = Emergency::create($validated);

        return redirect()
            ->route('emergencies.show', $emergency)
            ->with('success', "Emergencia {$emergency->folio} creada correctamente.");
    }

    public function show(Emergency $emergency): View
    {
        $emergency->load([
            'assignedTeam.members',
            'assignedUser',
            'createdBy',
            'photos',
            'history.user',
            'vehicles',
        ]);

        $allowedTransitions = Emergency::getAllowedTransitions($emergency->status, auth()->user()->role);
        $availableTeams     = Team::active()->get();
        $availableVehicles  = Vehicle::available()->get();

        return view('emergencies.show', compact('emergency', 'allowedTransitions', 'availableTeams', 'availableVehicles'));
    }

    public function edit(Emergency $emergency): View
    {
        $teams = Team::active()->get();
        return view('emergencies.edit', compact('emergency', 'teams'));
    }

    public function update(Request $request, Emergency $emergency): RedirectResponse
    {
        $validated = $request->validate([
            'type'              => ['required', 'in:incendio,accidente_transito,rescate,inundacion,emergencia_medica,derrumbe,otro'],
            'priority'          => ['required', 'in:baja,media,alta,critica'],
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'address'           => ['required', 'string', 'max:500'],
            'sector'            => ['nullable', 'string', 'max:200'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            'reported_by_name'  => ['nullable', 'string', 'max:200'],
            'reported_by_phone' => ['nullable', 'string', 'max:50'],
            'affected_people'   => ['required', 'integer', 'min:0'],
            'assigned_team_id'  => ['nullable', 'exists:teams,id'],
            'notes'             => ['nullable', 'string'],
        ]);

        $emergency->update($validated);

        return redirect()
            ->route('emergencies.show', $emergency)
            ->with('success', 'Emergencia actualizada correctamente.');
    }

    public function destroy(Emergency $emergency): RedirectResponse
    {
        $emergency->delete();
        return redirect()->route('emergencies.index')->with('success', 'Emergencia eliminada.');
    }

    public function changeStatus(Request $request, Emergency $emergency): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:ingresada,en_proceso,atendida,cerrada,cancelada'],
        ]);

        $allowed = Emergency::getAllowedTransitions($emergency->status, auth()->user()->role);

        if (! in_array($request->status, $allowed)) {
            return back()->with('error', 'No tienes permisos para realizar este cambio de estado.');
        }

        $emergency->update(['status' => $request->status]);

        return back()->with('success', 'Estado actualizado a "' . $emergency->fresh()->getStatusLabel() . '".');
    }

    public function assignTeam(Request $request, Emergency $emergency): RedirectResponse
    {
        $request->validate([
            'assigned_team_id' => ['required', 'exists:teams,id'],
        ]);

        $emergency->update([
            'assigned_team_id' => $request->assigned_team_id,
            'status'           => $emergency->status === 'ingresada' ? 'en_proceso' : $emergency->status,
        ]);

        // Enviar alerta WhatsApp al líder del equipo asignado
        $team = Team::with('leader')->find($request->assigned_team_id);

        if ($team && $team->leader && filled($team->leader->phone)) {
            $message = SendWhatsAppAlert::buildMessage($emergency->fresh());
            SendWhatsAppAlert::dispatch(
                $emergency->fresh(),
                $team->leader->phone,
                $team->leader->name,
                $message,
            );
        }

        return back()->with('success', 'Equipo asignado correctamente.');
    }

    public function uploadPhotos(Request $request, Emergency $emergency): RedirectResponse
    {
        $request->validate([
            'photos'   => ['required', 'array', 'max:10'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        foreach ($request->file('photos') as $photo) {
            $path   = $photo->store("emergencies/{$emergency->id}/photos", 'public');
            $sizeKb = (int) round($photo->getSize() / 1024);

            $emergency->photos()->create([
                'path'        => $path,
                'filename'    => $photo->getClientOriginalName(),
                'mime_type'   => $photo->getMimeType(),
                'size_kb'     => $sizeKb,
                'source'      => auth()->user()->role === 'lider' ? 'lider' : 'coordinador',
                'uploaded_by' => auth()->id(),
            ]);
        }

        return back()->with('success', count($request->file('photos')) . ' foto(s) subida(s) correctamente.');
    }

    public function deletePhoto(Emergency $emergency, EmergencyPhoto $photo): RedirectResponse
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Foto eliminada.');
    }

    public function history(Emergency $emergency): View
    {
        $history = $emergency->history()->with('user')->get();
        return view('emergencies.history', compact('emergency', 'history'));
    }
}
