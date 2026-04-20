<?php

namespace App\Http\Controllers;

use App\Exports\EmergenciesExport;
use App\Models\Emergency;
use App\Models\EmergencySupply;
use App\Models\Team;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $teams = Team::active()->orderBy('name')->get(['id', 'name']);
        return view('reports.index', compact('teams'));
    }

    public function emergencies(Request $request): View
    {
        $query = Emergency::with(['assignedTeam', 'createdBy'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->priority))
            ->when($request->filled('team_id'), fn ($q) => $q->where('assigned_team_id', $request->team_id))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByDesc('created_at');

        $emergencies = $query->paginate(25)->withQueryString();
        $teams       = Team::active()->orderBy('name')->get(['id', 'name']);

        return view('reports.emergencies', compact('emergencies', 'teams'));
    }

    public function exportPdf(Request $request): Response
    {
        $query = Emergency::with(['assignedTeam', 'createdBy'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->priority))
            ->when($request->filled('team_id'), fn ($q) => $q->where('assigned_team_id', $request->team_id))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByDesc('created_at');

        $emergencies = $query->get();
        $filters     = $request->only(['status', 'type', 'priority', 'team_id', 'date_from', 'date_to']);

        $pdf = Pdf::loadView('reports.pdf.emergencies', compact('emergencies', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-emergencias-' . now()->format('Ymd-His') . '.pdf');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $filename = 'reporte-emergencias-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new EmergenciesExport($request->all()), $filename);
    }

    public function inventory(Request $request): View
    {
        $query = EmergencySupply::with('team')
            ->when($request->filled('team_id'), fn ($q) => $q->where('team_id', $request->team_id))
            ->when($request->boolean('low_stock'), fn ($q) => $q->belowMinimum())
            ->when($request->boolean('expiring'), fn ($q) => $q->expiringSoon(30))
            ->orderByRaw('stock_current <= stock_minimum DESC')
            ->orderBy('name');

        $supplies    = $query->get();
        $teams       = Team::active()->orderBy('name')->get(['id', 'name']);
        $lowStockQty = EmergencySupply::belowMinimum()->count();

        return view('reports.inventory', compact('supplies', 'teams', 'lowStockQty'));
    }
}
