<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experience extends Model
{
    use HasFactory;

    protected $table = 'experiences';

    // status and moderation fields are set by moderators only.
    protected $fillable = [
        'user_id',
        'destination_id',
        'title',
        'body',
        'rating',
        'social_url',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'tags' => 'array',
            'moderated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'experience_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'experience_id');
    }
}