<?php

namespace App\Exports;

use App\Models\Emergency;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmergenciesExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function query(): Builder
    {
        return Emergency::with(['assignedTeam', 'createdBy'])
            ->when(!empty($this->filters['status']), fn ($q) => $q->where('status', $this->filters['status']))
            ->when(!empty($this->filters['type']), fn ($q) => $q->where('type', $this->filters['type']))
            ->when(!empty($this->filters['priority']), fn ($q) => $q->where('priority', $this->filters['priority']))
            ->when(!empty($this->filters['team_id']), fn ($q) => $q->where('assigned_team_id', $this->filters['team_id']))
            ->when(!empty($this->filters['date_from']), fn ($q) => $q->whereDate('created_at', '>=', $this->filters['date_from']))
            ->when(!empty($this->filters['date_to']), fn ($q) => $q->whereDate('created_at', '<=', $this->filters['date_to']))
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Tipo',
            'Prioridad',
            'Estado',
            'Dirección',
            'Equipo Asignado',
            'Afectados',
            'Fecha de Ingreso',
        ];
    }

    public function map($emergency): array
    {
        return [
            $emergency->folio,
            $emergency->getTypeLabel(),
            $emergency->getPriorityLabel(),
            $emergency->getStatusLabel(),
            $emergency->address,
            $emergency->assignedTeam?->name ?? 'Sin asignar',
            $emergency->affected_people,
            $emergency->created_at?->format('d/m/Y H:i'),
        ];
    }
}
