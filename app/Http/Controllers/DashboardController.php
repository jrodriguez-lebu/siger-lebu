<?php

namespace App\Http\Controllers;

use App\Models\Emergency;
use App\Models\EmergencySupply;
use App\Models\Team;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // ── Estadísticas generales ──────────────────────────────
        $stats = [
            'total_active'     => Emergency::whereIn('status', ['ingresada', 'en_proceso'])->count(),
            'total_today'      => Emergency::whereDate('created_at', today())->count(),
            'total_month'      => Emergency::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'teams_active'     => Team::active()->count(),
            'vehicles_available' => Vehicle::available()->count(),
            'low_stock'        => EmergencySupply::belowMinimum()->count(),
        ];

        // ── Emergencias activas recientes ───────────────────────
        $recentEmergencies = Emergency::with(['assignedTeam', 'createdBy'])
            ->when($user->role === 'lider', fn ($q) => $q->where('assigned_team_id', function ($sq) use ($user) {
                $sq->select('team_id')->from('team_members')->where('user_id', $user->id);
            }))
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // ── Distribución por tipo (para gráfico) ───────────────
        $byType = Emergency::select('type', DB::raw('count(*) as total'))
            ->whereMonth('created_at', now()->month)
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->type => $item->total]);

        // ── Distribución por estado ─────────────────────────────
        $byStatus = Emergency::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->status => $item->total]);

        // ── Emergencias por mes (últimos 6 meses) ───────────────
        $byMonth = Emergency::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // ── Insumos con stock crítico ───────────────────────────
        $criticalSupplies = EmergencySupply::belowMinimum()
            ->with('team')
            ->limit(5)
            ->get();

        // ── Vehículos con documentación por vencer ──────────────
        $vehiclesAlerts = Vehicle::needsService()->orWhere(function ($q) {
            $q->whereNotNull('insurance_expiry')->where('insurance_expiry', '<=', now()->addDays(30));
        })->limit(5)->get();

        return view('dashboard.index', compact(
            'stats',
            'recentEmergencies',
            'byType',
            'byStatus',
            'byMonth',
            'criticalSupplies',
            'vehiclesAlerts',
        ));
    }
}
