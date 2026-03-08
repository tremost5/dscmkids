<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'channel',
        'status',
        'target_count',
        'processed_count',
        'failed_count',
        'last_error',
        'sent_at',
        'sent_by',
    ];

    protected function casts(): array
    {
        return [
            'target_count' => 'integer',
            'processed_count' => 'integer',
            'failed_count' => 'integer',
            'sent_at' => 'datetime',
        ];
    }
}
