<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Emergency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'folio',
        'type',
        'priority',
        'status',
        'title',
        'description',
        'address',
        'sector',
        'commune',
        'latitude',
        'longitude',
        'reported_by_name',
        'reported_by_phone',
        'affected_people',
        'assigned_team_id',
        'assigned_user_id',
        'created_by',
        'started_at',
        'resolved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude'    => 'float',
            'longitude'   => 'float',
            'started_at'  => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    // ─── Scopes ────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['ingresada', 'en_proceso']);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    // ─── Relaciones ────────────────────────────────────────────
    public function assignedTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'assigned_team_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EmergencyPhoto::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(EmergencyHistory::class)->orderByDesc('created_at');
    }

    public function emergencyVehicles(): HasMany
    {
        return $this->hasMany(EmergencyVehicle::class);
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'emergency_vehicles')
            ->withPivot('assigned_at', 'released_at', 'assigned_by', 'notes');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(SigerNotification::class);
    }

    // ─── Helpers & Labels ─────────────────────────────────────
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'ingresada'   => 'Ingresada',
            'en_proceso'  => 'En Proceso',
            'atendida'    => 'Atendida',
            'cerrada'     => 'Cerrada',
            'cancelada'   => 'Cancelada',
            default       => ucfirst($this->status),
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'ingresada'   => 'blue',
            'en_proceso'  => 'yellow',
            'atendida'    => 'green',
            'cerrada'     => 'gray',
            'cancelada'   => 'red',
            default       => 'gray',
        };
    }

    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            'baja'    => 'Baja',
            'media'   => 'Media',
            'alta'    => 'Alta',
            'critica' => 'Crítica',
            default   => ucfirst($this->priority),
        };
    }

    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            'baja'    => 'green',
            'media'   => 'yellow',
            'alta'    => 'orange',
            'critica' => 'red',
            default   => 'gray',
        };
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'incendio'           => 'Incendio',
            'accidente_transito' => 'Accidente de Tránsito',
            'rescate'            => 'Rescate',
            'inundacion'         => 'Inundación',
            'emergencia_medica'  => 'Emergencia Médica',
            'derrumbe'           => 'Derrumbe',
            'otro'               => 'Otro',
            default              => ucfirst($this->type),
        };
    }

    public function getTypeIcon(): string
    {
        return match ($this->type) {
            'incendio'           => '🔥',
            'accidente_transito' => '🚗',
            'rescate'            => '🆘',
            'inundacion'         => '🌊',
            'emergencia_medica'  => '🏥',
            'derrumbe'           => '⛰️',
            default              => '⚠️',
        };
    }

    public function getMapMarkerColor(): string
    {
        return match ($this->priority) {
            'critica' => '#dc2626',
            'alta'    => '#ea580c',
            'media'   => '#ca8a04',
            'baja'    => '#16a34a',
            default   => '#6b7280',
        };
    }

    public function hasCoordinates(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    // Transiciones de estado válidas por rol
    public static function getAllowedTransitions(string $currentStatus, string $userRole): array
    {
        $transitions = [
            'ingresada' => [
                'coordinador' => ['en_proceso', 'cancelada'],
                'admin'       => ['en_proceso', 'cancelada'],
                'digitador'   => [],
                'lider'       => [],
            ],
            'en_proceso' => [
                'coordinador' => ['atendida', 'cancelada'],
                'admin'       => ['atendida', 'cancelada'],
                'lider'       => ['atendida'],
                'digitador'   => [],
            ],
            'atendida' => [
                'coordinador' => ['cerrada', 'en_proceso'],
                'admin'       => ['cerrada', 'en_proceso'],
                'lider'       => [],
                'digitador'   => [],
            ],
            'cerrada'   => ['coordinador' => [], 'admin' => [], 'lider' => [], 'digitador' => []],
            'cancelada' => ['coordinador' => [], 'admin' => [], 'lider' => [], 'digitador' => []],
        ];

        return $transitions[$currentStatus][$userRole] ?? [];
    }
}
