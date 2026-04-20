<?php

namespace App\Http\Controllers;

use App\Models\Emergency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(): View
    {
        return view('map.index');
    }

    public function geojson(): JsonResponse
    {
        $emergencies = Emergency::withCoordinates()
            ->whereNotIn('status', ['cerrada', 'cancelada'])
            ->with(['assignedTeam', 'photos'])
            ->get();

        $features = $emergencies->map(function (Emergency $e) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [$e->longitude, $e->latitude],
                ],
                'properties' => [
                    'id'          => $e->id,
                    'folio'       => $e->folio,
                    'title'       => $e->title,
                    'type'        => $e->type,
                    'type_label'  => $e->getTypeLabel(),
                    'type_icon'   => $e->getTypeIcon(),
                    'priority'    => $e->priority,
                    'status'      => $e->status,
                    'status_label'=> $e->getStatusLabel(),
                    'address'     => $e->address,
                    'affected'    => $e->affected_people,
                    'team'        => $e->assignedTeam?->name,
                    'color'       => $e->getMapMarkerColor(),
                    'created_at'  => $e->created_at->diffForHumans(),
                    'url'         => route('emergencies.show', $e),
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function updateCoordinates(Request $request, Emergency $emergency): JsonResponse
    {
        $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $emergency->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['success' => true]);
    }
}
