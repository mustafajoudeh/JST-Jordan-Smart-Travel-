<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'governorate_id',
        'destination_id',
        'name',
        'description',
        'event_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue_name',
        'latitude',
        'longitude',
        'source_type',
        'source_url',
        'last_verified_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'last_verified_at' => 'date',
        ];
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tripItems(): HasMany
    {
        return $this->hasMany(TripItems::class, 'event_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'event_id');
    }
}