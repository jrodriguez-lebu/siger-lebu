<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'color',
        'status',
        'leader_id',
    ];

    // ─── Scopes ────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    // ─── Relaciones ────────────────────────────────────────────
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('role_in_team', 'joined_at')
            ->withTimestamps();
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function supplies(): HasMany
    {
        return $this->hasMany(EmergencySupply::class);
    }

    public function emergencies(): HasMany
    {
        return $this->hasMany(Emergency::class, 'assigned_team_id');
    }

    public function personnel(): HasMany
    {
        return $this->hasMany(\App\Models\Personnel::class)->orderBy('name');
    }

    // ─── Helpers ───────────────────────────────────────────────
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'rescate'   => 'Rescate',
            'tecnico'   => 'Técnico',
            'bombero'   => 'Bomberos',
            'medico'    => 'Médico',
            'apoyo'     => 'Apoyo',
            default     => 'Otro',
        };
    }

    public function getMembersCountAttribute(): int
    {
        return $this->teamMembers()->count();
    }
}
