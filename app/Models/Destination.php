<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory;

    protected $table = 'destinations';

    protected $fillable = [
        'governorate_id',
        'category_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'address',
        'suggested_duration_min',
        'estimated_cost',
        'opening_hours',
        'tags',
        'source_type',
        'source_url',
        'last_verified_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'suggested_duration_min' => 'integer',
            'estimated_cost' => 'decimal:2',
            'opening_hours' => 'array',
            'tags' => 'array',
            'last_verified_at' => 'date',
        ];
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'destination_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'destination_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'destination_id');
    }

    public function tripItems(): HasMany
    {
        return $this->hasMany(TripItems::class, 'destination_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'destination_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'destination_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'destination_id');
    }
}