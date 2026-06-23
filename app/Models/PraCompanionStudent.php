<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PraCompanionStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'pra_companion_id',
        'event_registration_id',
    ];

    public function companion(): BelongsTo
    {
        return $this->belongsTo(PraCompanion::class, 'pra_companion_id');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }
}
