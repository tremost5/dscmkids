<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyQuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_quiz_bank_id',
        'question_text',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(DailyQuizBank::class, 'daily_quiz_bank_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(DailyQuizOption::class)->orderBy('sort_order');
    }
}

