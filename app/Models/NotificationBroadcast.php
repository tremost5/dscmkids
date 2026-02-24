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
        'target_count',
        'sent_at',
        'sent_by',
    ];

    protected function casts(): array
    {
        return [
            'target_count' => 'integer',
            'sent_at' => 'datetime',
        ];
    }
}

