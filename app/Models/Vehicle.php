<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'plate',
        'type',
        'brand',
        'model',
        'year',
        'color',
        'capacity',
        'status',
        'team_id',
        'fuel_type',
        'current_mileage',
        'last_service_date',
        'next_service_date',
        'insurance_expiry',
        'technical_review_expiry',
        'gps_tracking_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'last_service_date'       => 'date',
            'next_service_date'       => 'date',
            'insurance_expiry'        => 'date',
            'technical_review_expiry' => 'date',
        ];
    }

    // ─── Scopes ────────────────────────────────────────────────
    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    public function scopeNeedsService($query)
    {
        return $query->whereNotNull('next_service_date')
            ->where('next_service_date', '<=', now()->addDays(30));
    }

    // ─── Relaciones ────────────────────────────────────────────
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function emergencyVehicles(): HasMany
    {
        return $this->hasMany(EmergencyVehicle::class);
    }

    public function emergencies(): BelongsToMany
    {
        return $this->belongsToMany(Emergency::class, 'emergency_vehicles')
            ->withPivot('assigned_at', 'released_at', 'assigned_by', 'notes');
    }

    // ─── Helpers ───────────────────────────────────────────────
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'ambulancia'       => 'Ambulancia',
            'camion_bomberos'  => 'Camión de Bomberos',
            'camioneta'        => 'Camioneta',
            'furgon'           => 'Furgón',
            'moto'             => 'Motocicleta',
            'helicoptero'      => 'Helicóptero',
            'bote'             => 'Bote',
            default            => 'Otro',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'disponible'        => 'Disponible',
            'en_servicio'       => 'En Servicio',
            'mantenimiento'     => 'En Mantenimiento',
            'fuera_de_servicio' => 'Fuera de Servicio',
            default             => ucfirst($this->status),
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'disponible'        => 'green',
            'en_servicio'       => 'blue',
            'mantenimiento'     => 'yellow',
            'fuera_de_servicio' => 'red',
            default             => 'gray',
        };
    }

    public function isInsuranceExpiringSoon(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry->lte(now()->addDays(30));
    }

    public function isTechnicalReviewExpiringSoon(): bool
    {
        return $this->technical_review_expiry && $this->technical_review_expiry->lte(now()->addDays(30));
    }
}
