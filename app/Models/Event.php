<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'description',
        'location_name',
        'location_description',
        'benefits',
        'schedule',
        'payment_info',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'schedule' => 'array',
            'payment_info' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function groups(): HasMany
    {
        return $this->hasMany(EventGroup::class)->orderBy('sort_order');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function banner(): HasOne
    {
        return $this->hasOne(EventBanner::class)->where('is_active', true)->latestOfMany();
    }

    public function banners(): HasMany
    {
        return $this->hasMany(EventBanner::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(EventGallery::class)->orderBy('sort_order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(EventVideo::class)->orderBy('sort_order');
    }
}
