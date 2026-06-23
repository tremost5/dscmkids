<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PraCompanion extends Model
{
    use HasFactory;

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PENDING = 'pending_verification';
    public const PAYMENT_PAID = 'paid';

    protected $fillable = [
        'companion_name',
        'whatsapp_number',
        'attend_26_june',
        'attend_27_june',
        'payment_method',
        'payment_status',
        'payment_proof_path',
    ];

    protected function casts(): array
    {
        return [
            'attend_26_june' => 'boolean',
            'attend_27_june' => 'boolean',
        ];
    }

    public function companionStudents(): HasMany
    {
        return $this->hasMany(PraCompanionStudent::class);
    }

    public function students(): HasMany
    {
        return $this->companionStudents();
    }

    public function registrations(): BelongsToMany
    {
        return $this->belongsToMany(EventRegistration::class, 'pra_companion_students')
            ->withTimestamps();
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'Lunas',
            self::PAYMENT_PENDING => 'Menunggu Verifikasi',
            default => 'Belum Bayar',
        };
    }

    public function paymentMethodLabel(): string
    {
        return $this->payment_method === 'cash' ? 'Tunai' : 'Transfer BCA';
    }

    public function attendanceDatesLabel(): string
    {
        $dates = [];

        if ($this->attend_26_june) {
            $dates[] = '26 Juni 2026';
        }

        if ($this->attend_27_june) {
            $dates[] = '27 Juni 2026';
        }

        return $dates === [] ? '-' : implode(', ', $dates);
    }
}
