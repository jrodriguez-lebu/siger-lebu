<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'emergency_id',
        'recipient_name',
        'phone',
        'message',
        'status',
        'provider',
        'error',
    ];

    public function emergency(): BelongsTo
    {
        return $this->belongsTo(Emergency::class);
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'enviado'   => 'badge-green',
            'fallido'   => 'badge-red',
            default     => 'badge-yellow',
        };
    }
}
