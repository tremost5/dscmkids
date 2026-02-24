<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'class_group',
        'email',
        'password',
        'role',
        'points',
        'streak_days',
        'last_quiz_played_on',
        'last_daily_reset_seen_on',
        'last_weekly_claimed_on',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_quiz_played_on' => 'date',
            'last_daily_reset_seen_on' => 'date',
            'last_weekly_claimed_on' => 'date',
        ];
    }

    public function dailyQuizResults(): HasMany
    {
        return $this->hasMany(DailyQuizResult::class);
    }

    public function rewardClaims(): HasMany
    {
        return $this->hasMany(StudentRewardClaim::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
