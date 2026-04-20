<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'brand',
        'model',
        'serial_number',
        'status',
        'team_id',
        'purchase_date',
        'last_maintenance',
        'next_maintenance',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date'    => 'date',
            'last_maintenance' => 'date',
            'next_maintenance' => 'date',
        ];
    }

    // ─── Scopes ────────────────────────────────────────────────
    public function scopeNeedsMaintenance($query)
    {
        return $query->whereNotNull('next_maintenance')
            ->where('next_maintenance', '<=', now()->addDays(15));
    }

    // ─── Relaciones ────────────────────────────────────────────
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // ─── Helpers ───────────────────────────────────────────────
    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'herramienta'    => 'Herramienta',
            'equipo_medico'  => 'Equipo Médico',
            'equipo_rescate' => 'Equipo de Rescate',
            'comunicacion'   => 'Comunicación',
            'proteccion'     => 'Protección',
            default          => 'Otro',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'disponible'    => 'Disponible',
            'en_uso'        => 'En Uso',
            'mantenimiento' => 'En Mantenimiento',
            'dado_de_baja'  => 'Dado de Baja',
            default         => ucfirst($this->status),
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'disponible'    => 'green',
            'en_uso'        => 'blue',
            'mantenimiento' => 'yellow',
            'dado_de_baja'  => 'red',
            default         => 'gray',
        };
    }

    public function needsMaintenanceSoon(): bool
    {
        return $this->next_maintenance && $this->next_maintenance->lte(now()->addDays(15));
    }
}
