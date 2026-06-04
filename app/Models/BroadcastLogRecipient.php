<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastLogRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'broadcast_log_id',
        'phone',
        'recipient_name',
        'status',
        'response',
    ];

    public function broadcastLog(): BelongsTo
    {
        return $this->belongsTo(BroadcastLog::class);
    }
}
