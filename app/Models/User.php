<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'active',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'active'            => 'boolean',
        ];
    }

    // ─── Scopes ────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    // ─── Relaciones ────────────────────────────────────────────
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot('role_in_team', 'joined_at')
            ->withTimestamps();
    }

    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    public function createdEmergencies(): HasMany
    {
        return $this->hasMany(Emergency::class, 'created_by');
    }

    public function assignedEmergencies(): HasMany
    {
        return $this->hasMany(Emergency::class, 'assigned_user_id');
    }

    public function sigerNotifications(): HasMany
    {
        return $this->hasMany(SigerNotification::class);
    }

    public function emergencyHistory(): HasMany
    {
        return $this->hasMany(EmergencyHistory::class);
    }

    // ─── Helpers ───────────────────────────────────────────────
    public function getRoleLabel(): string
    {
        return match ($this->role) {
            'admin'        => 'Super Admin',
            'coordinador'  => 'Coordinador',
            'lider'        => 'Líder de Equipo',
            'digitador'    => 'Digitador',
            default        => ucfirst($this->role ?? ''),
        };
    }

    public function getRoleBadgeColor(): string
    {
        return match ($this->role) {
            'admin'        => 'red',
            'coordinador'  => 'blue',
            'lider'        => 'green',
            'digitador'    => 'yellow',
            default        => 'gray',
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1e40af&color=fff&bold=true';
    }
}
