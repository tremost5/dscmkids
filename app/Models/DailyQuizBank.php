<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyQuizBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_key',
        'title',
        'memory_verse',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DailyQuizQuestion::class)->orderBy('sort_order');
    }
}

