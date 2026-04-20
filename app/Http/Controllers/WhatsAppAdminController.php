<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = WhatsAppLog::with('emergency')
            ->latest();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $stats = [
            'total'     => WhatsAppLog::count(),
            'enviados'  => WhatsAppLog::where('status', 'enviado')->count(),
            'fallidos'  => WhatsAppLog::where('status', 'fallido')->count(),
            'pendientes'=> WhatsAppLog::where('status', 'pendiente')->count(),
        ];

        $config = [
            'enabled'  => config('services.whatsapp.enabled'),
            'provider' => config('services.whatsapp.provider'),
        ];

        return view('whatsapp.index', compact('logs', 'stats', 'config'));
    }
}
