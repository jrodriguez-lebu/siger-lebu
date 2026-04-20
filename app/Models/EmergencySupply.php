<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmergencySupply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'category',
        'unit',
        'stock_current',
        'stock_minimum',
        'stock_maximum',
        'team_id',
        'location',
        'expiry_date',
        'supplier',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'stock_current' => 'float',
            'stock_minimum' => 'float',
            'stock_maximum' => 'float',
            'unit_cost'     => 'float',
            'expiry_date'   => 'date',
        ];
    }

    // ─── Scopes ────────────────────────────────────────────────
    public function scopeBelowMinimum($query)
    {
        return $query->whereColumn('stock_current', '<=', 'stock_minimum');
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days));
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
            'medicamento'      => 'Medicamento',
            'material_curacion'=> 'Material de Curación',
            'oxigeno'          => 'Oxígeno',
            'combustible'      => 'Combustible',
            'alimento'         => 'Alimento',
            'ropa'             => 'Ropa/Vestuario',
            'herramienta'      => 'Herramienta',
            default            => 'Otro',
        };
    }

    public function isLowStock(): bool
    {
        return $this->stock_current <= $this->stock_minimum;
    }

    public function getStockPercentage(): float
    {
        if (! $this->stock_maximum || $this->stock_maximum == 0) {
            return 100;
        }

        return round(($this->stock_current / $this->stock_maximum) * 100, 1);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && $this->expiry_date->lte(now()->addDays($days));
    }
}
