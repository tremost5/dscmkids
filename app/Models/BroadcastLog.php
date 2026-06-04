<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BroadcastLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'filter',
        'message',
        'total_recipients',
        'success_count',
        'failed_count',
    ];

    protected function casts(): array
    {
        return [
            'total_recipients' => 'integer',
            'success_count' => 'integer',
            'failed_count' => 'integer',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(BroadcastLogRecipient::class);
    }
}
