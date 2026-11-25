<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    // status, review fields, booking_enabled and commission_rate are admin/business
    // decisions and are intentionally not mass assignable.
    protected $fillable = [
        'provider_id',
        'destination_id',
        'category_id',
        'name',
        'description',
        'duration_min',
        'price',
        'price_unit',
        'max_participants',
        'daily_capacity',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'duration_min' => 'integer',
            'price' => 'decimal:3',
            'booking_enabled' => 'boolean',
            'max_participants' => 'integer',
            'daily_capacity' => 'integer',
            'commission_rate' => 'decimal:4',
            'reviewed_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'service_id');
    }

    public function tripItems(): HasMany
    {
        return $this->hasMany(TripItems::class, 'service_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'service_id');
    }
}