<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorites';

    public const UPDATED_AT = null;

    // Exactly one of destination_id / service_id / experience_id must be set
    // (enforced by the favorites_exactly_one_target_check constraint).
    protected $fillable = [
        'user_id',
        'destination_id',
        'service_id',
        'experience_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }
}