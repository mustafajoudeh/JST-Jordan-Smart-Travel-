<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    // Price snapshot, commission and provider net are written by the booking service
    // (never from request input); status/payment_status/cancellation are set explicitly.
    protected $fillable = [
        'user_id',
        'service_id',
        'booking_date',
        'start_time',
        'participants',
        'unit_price',
        'total_price',
        'currency',
        'commission_rate',
        'commission_amount',
        'provider_net',
        'contact_phone',
        'customer_note',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'participants' => 'integer',
            'unit_price' => 'decimal:3',
            'total_price' => 'decimal:3',
            'commission_rate' => 'decimal:4',
            'commission_amount' => 'decimal:3',
            'provider_net' => 'decimal:3',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // The provider is derived through services.provider_id (bookings has no provider column).
    public function provider(): HasOneThrough
    {
        return $this->hasOneThrough(Provider::class, Service::class, 'id', 'id', 'service_id', 'provider_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    // trip_items.booking_id is unique, so a booking belongs to at most one trip item.
    public function tripItem(): HasOne
    {
        return $this->hasOne(TripItems::class, 'booking_id');
    }
}