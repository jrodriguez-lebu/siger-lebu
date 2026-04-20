<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SigerNotification extends Model
{
    protected $table = 'siger_notifications';

    protected $fillable = [
        'emergency_id',
        'user_id',
        'channel',
        'type',
        'message',
        'status',
        'sent_at',
        'read_at',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'sent_at'  => 'datetime',
            'read_at'  => 'datetime',
            'metadata' => 'array',
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

    public function markAsRead(): void
    {
        $this->update(['status' => 'leido', 'read_at' => now()]);
    }

    public function markAsSent(): void
    {
        $this->update(['status' => 'enviado', 'sent_at' => now()]);
    }
}
