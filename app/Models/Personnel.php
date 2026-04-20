<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personnel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'personnel';

    protected $fillable = [
        'name',
        'rut',
        'specialty',
        'position',
        'phone',
        'email',
        'team_id',
        'is_active',
        'joined_date',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'joined_date' => 'date',
        ];
    }

    // ─── Relationships ────────────────────────────────────────
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // ─── Scopes ───────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Labels ───────────────────────────────────────────────
    public function getSpecialtyLabel(): string
    {
        return match($this->specialty) {
            'bombero'        => '🔴 Bombero',
            'paramedico'     => '🚑 Paramédico',
            'enfermero'      => '🏥 Enfermero/a',
            'medico'         => '⚕️ Médico',
            'rescatista'     => '🆘 Rescatista',
            'logistica'      => '📦 Logística',
            'comunicaciones' => '📡 Comunicaciones',
            'carabinero'     => '👮 Carabinero',
            'voluntario'     => '🤝 Voluntario',
            default          => '👤 Otro',
        };
    }

    public function getSpecialtyColor(): string
    {
        return match($this->specialty) {
            'bombero'        => 'badge-red',
            'paramedico',
            'enfermero',
            'medico'         => 'badge-green',
            'rescatista'     => 'badge-amber',
            'logistica'      => 'badge-yellow',
            'comunicaciones' => 'badge-blue',
            'carabinero'     => 'badge-blue',
            default          => 'badge-gray',
        };
    }
}
