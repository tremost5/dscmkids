<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyQuizResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_date',
        'quiz_key',
        'score',
        'correct_answers',
        'total_questions',
        'badge_awarded',
        'answers',
    ];

    protected function casts(): array
    {
        return [
            'quiz_date' => 'date',
            'answers' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

