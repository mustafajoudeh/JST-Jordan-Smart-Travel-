<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = [
        'destination_id',
        'category_id',
        'name',
        'description',
        'duration_min',
        'estimated_cost',
        'available_times',
        'source_type',
        'source_url',
        'last_verified_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'duration_min' => 'integer',
            'estimated_cost' => 'decimal:2',
            'available_times' => 'array',
            'last_verified_at' => 'date',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tripItems(): HasMany
    {
        return $this->hasMany(TripItems::class, 'activity_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'activity_id');
    }
}