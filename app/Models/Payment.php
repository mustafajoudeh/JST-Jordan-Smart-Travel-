<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    // Append-only ledger: a succeeded charge is never edited; a refund is a new row.
    protected $fillable = [
        'booking_id',
        'type',
        'parent_payment_id',
        'amount',
        'currency',
        'method',
        'gateway',
        'gateway_reference',
        'status',
        'failure_reason',
        'initiated_by',
        'paid_at',
    ];

    // Database-generated helper column for the "one succeeded charge per booking" unique index.
    protected $hidden = [
        'succeeded_charge_booking_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
            'paid_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function parentPayment(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_payment_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(self::class, 'parent_payment_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
}