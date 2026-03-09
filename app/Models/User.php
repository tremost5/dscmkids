<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;

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
        'is_active',
        'points',
        'streak_days',
        'last_quiz_played_on',
        'last_daily_reset_seen_on',
        'last_weekly_claimed_on',
        'last_login_at',
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
            'is_active' => 'boolean',
            'password' => 'hashed',
            'last_quiz_played_on' => 'date',
            'last_daily_reset_seen_on' => 'date',
            'last_weekly_claimed_on' => 'date',
            'last_login_at' => 'datetime',
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
        return in_array($this->role, ['super_admin', 'admin', 'editor'], true);
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $permissions = Arr::get(config('admin_permissions.roles'), $this->role.'.permissions', []);

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    public function roleLabel(): string
    {
        return (string) Arr::get(config('admin_permissions.roles'), $this->role.'.label', ucfirst((string) $this->role));
    }
}
