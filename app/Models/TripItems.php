<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripItems extends Model
{
    use HasFactory;

    protected $table = 'trip_items';

    // Exactly one of destination_id / activity_id / event_id / service_id must be set;
    // booking_id requires service_id (enforced by CHECK constraints).
    protected $fillable = [
        'trip_id',
        'day_number',
        'position',
        'start_time',
        'end_time',
        'destination_id',
        'activity_id',
        'event_id',
        'service_id',
        'booking_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'day_number' => 'integer',
            'position' => 'integer',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}