<?php

namespace App\Observers;

use App\Models\Emergency;
use App\Models\EmergencyHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmergencyObserver
{
    public function creating(Emergency $emergency): void
    {
        // Auto-generar folio único: EMG-YYYY-NNNNN
        $year  = now()->year;
        $count = DB::table('emergencies')
            ->whereYear('created_at', $year)
            ->count() + 1;

        $emergency->folio = 'EMG-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function created(Emergency $emergency): void
    {
        EmergencyHistory::create([
            'emergency_id' => $emergency->id,
            'user_id'      => Auth::id(),
            'action'       => 'creacion',
            'new_value'    => json_encode($emergency->toArray()),
            'description'  => 'Emergencia ingresada al sistema con folio ' . $emergency->folio,
            'ip_address'   => request()->ip(),
        ]);
    }

    public function updated(Emergency $emergency): void
    {
        $dirty = $emergency->getDirty();

        if (empty($dirty)) {
            return;
        }

        // Registrar cambio de estado específicamente
        if (isset($dirty['status'])) {
            EmergencyHistory::create([
                'emergency_id' => $emergency->id,
                'user_id'      => Auth::id(),
                'action'       => 'cambio_estado',
                'old_value'    => json_encode(['status' => $emergency->getOriginal('status')]),
                'new_value'    => json_encode(['status' => $emergency->status]),
                'description'  => 'Estado cambiado de "' . $emergency->getOriginal('status') . '" a "' . $emergency->status . '"',
                'ip_address'   => request()->ip(),
            ]);

            // Actualizar timestamps según estado
            if ($emergency->status === 'en_proceso' && ! $emergency->started_at) {
                $emergency->started_at = now();
            }
            if (in_array($emergency->status, ['cerrada', 'atendida']) && ! $emergency->resolved_at) {
                $emergency->resolved_at = now();
            }
        }

        // Registrar asignación de equipo
        if (isset($dirty['assigned_team_id'])) {
            EmergencyHistory::create([
                'emergency_id' => $emergency->id,
                'user_id'      => Auth::id(),
                'action'       => 'asignacion_equipo',
                'old_value'    => json_encode(['team_id' => $emergency->getOriginal('assigned_team_id')]),
                'new_value'    => json_encode(['team_id' => $emergency->assigned_team_id]),
                'description'  => 'Equipo asignado a la emergencia',
                'ip_address'   => request()->ip(),
            ]);
        }

        // Registrar cambio de prioridad
        if (isset($dirty['priority'])) {
            EmergencyHistory::create([
                'emergency_id' => $emergency->id,
                'user_id'      => Auth::id(),
                'action'       => 'prioridad_cambiada',
                'old_value'    => json_encode(['priority' => $emergency->getOriginal('priority')]),
                'new_value'    => json_encode(['priority' => $emergency->priority]),
                'description'  => 'Prioridad cambiada de "' . $emergency->getOriginal('priority') . '" a "' . $emergency->priority . '"',
                'ip_address'   => request()->ip(),
            ]);
        }
    }
}
