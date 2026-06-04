<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventRegistration extends Model
{
    use HasFactory;

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PENDING = 'pending_verification';
    public const PAYMENT_PAID = 'paid';

    public const ATTENDANCE_PRESENT = 'present';
    public const ATTENDANCE_ABSENT = 'absent';

    protected $fillable = [
        'event_id',
        'event_group_id',
        'full_name',
        'nickname',
        'has_allergy',
        'allergy_notes',
        'birth_date',
        'gender',
        'class_before',
        'church_branch',
        'parent_name',
        'whatsapp_number',
        'address',
        'payment_method',
        'payment_status',
        'attendance_status',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'has_allergy' => 'boolean',
            'birth_date' => 'date',
            'registered_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(EventGroup::class, 'event_group_id');
    }

    public function paymentProof(): HasOne
    {
        return $this->hasOne(PaymentProof::class);
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'Lunas',
            self::PAYMENT_PENDING => 'Menunggu Verifikasi',
            default => 'Belum Bayar',
        };
    }

    public function attendanceStatusLabel(): string
    {
        return $this->attendance_status === self::ATTENDANCE_PRESENT ? 'Sudah Hadir' : 'Belum Hadir';
    }
}
