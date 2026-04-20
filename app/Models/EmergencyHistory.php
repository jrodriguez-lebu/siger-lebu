<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyHistory extends Model
{
    protected $table = 'emergency_history';

    public $timestamps = false;

    const UPDATED_AT = null;

    protected $fillable = [
        'emergency_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
        'description',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function emergency(): BelongsTo
    {
        return $this->belongsTo(Emergency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabel(): string
    {
        return match ($this->action) {
            'creacion'          => 'Emergencia creada',
            'cambio_estado'     => 'Cambio de estado',
            'asignacion_equipo' => 'Equipo asignado',
            'asignacion_vehiculo' => 'Vehículo asignado',
            'foto_subida'       => 'Foto subida',
            'actualizacion'     => 'Datos actualizados',
            'prioridad_cambiada'=> 'Prioridad modificada',
            default             => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}
