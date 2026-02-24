<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArcadeGameScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_key',
        'played_on',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'played_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

