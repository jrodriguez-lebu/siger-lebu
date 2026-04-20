<?php

namespace App\Http\Controllers;

use App\Models\Emergency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        return view('public.index');
    }

    public function reportForm(): View
    {
        return view('public.report');
    }

    public function reportStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reported_by_name'  => ['required', 'string', 'max:200'],
            'reported_by_phone' => ['required', 'string', 'max:50'],
            'address'           => ['required', 'string', 'max:500'],
            'sector'            => ['nullable', 'string', 'max:200'],
            'type'              => ['required', 'in:incendio,accidente_transito,rescate,inundacion,emergencia_medica,derrumbe,otro'],
            'affected_people'   => ['required', 'integer', 'min:0', 'max:9999'],
            'description'       => ['required', 'string', 'min:10', 'max:2000'],
            'photos'            => ['nullable', 'array', 'max:5'],
            'photos.*'          => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ], [
            'reported_by_name.required'  => 'El nombre es obligatorio.',
            'reported_by_phone.required' => 'El teléfono es obligatorio.',
            'address.required'           => 'La dirección es obligatoria.',
            'type.required'              => 'Selecciona el tipo de emergencia.',
            'affected_people.required'   => 'Indica la cantidad de personas afectadas.',
            'description.required'       => 'Describe la emergencia.',
            'description.min'            => 'La descripción debe tener al menos 10 caracteres.',
            'photos.*.max'               => 'Cada foto no puede superar los 5MB.',
            'photos.*.image'             => 'Solo se permiten archivos de imagen.',
        ]);

        // Generar título automático
        $typeLabels = [
            'incendio'           => 'Incendio',
            'accidente_transito' => 'Accidente de Tránsito',
            'rescate'            => 'Rescate',
            'inundacion'         => 'Inundación',
            'emergencia_medica'  => 'Emergencia Médica',
            'derrumbe'           => 'Derrumbe',
            'otro'               => 'Emergencia',
        ];

        $validated['title']    = ($typeLabels[$validated['type']] ?? 'Emergencia') . ' en ' . $validated['address'];
        $validated['status']   = 'ingresada';
        $validated['priority'] = 'media';
        $validated['commune']  = 'Lebu';

        $emergency = Emergency::create($validated);

        // Procesar fotos subidas
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path     = $photo->store("emergencies/{$emergency->id}/photos", 'public');
                $sizeKb   = (int) round($photo->getSize() / 1024);

                $emergency->photos()->create([
                    'path'      => $path,
                    'filename'  => $photo->getClientOriginalName(),
                    'mime_type' => $photo->getMimeType(),
                    'size_kb'   => $sizeKb,
                    'source'    => 'publico',
                ]);
            }
        }

        return redirect()->route('public.thank-you')
            ->with('folio', $emergency->folio);
    }

    public function thankYou(): View
    {
        return view('public.thank-you', [
            'folio' => session('folio'),
        ]);
    }

    public function map(): View
    {
        $emergencies = Emergency::active()
            ->withCoordinates()
            ->with('assignedTeam')
            ->get();

        return view('public.map', compact('emergencies'));
    }
}
