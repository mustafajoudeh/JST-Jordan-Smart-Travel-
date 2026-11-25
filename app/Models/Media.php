<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    // Set explicitly: "media" is its own plural and must not be guessed.
    protected $table = 'media';

    public const UPDATED_AT = null;

    // Exactly one of destination_id / activity_id / event_id / experience_id must be set
    // (enforced by media_exactly_one_parent_check).
    protected $fillable = [
        'file_path',
        'mime_type',
        'file_size_bytes',
        'is_primary',
        'sort_order',
        'destination_id',
        'activity_id',
        'event_id',
        'experience_id',
        'uploaded_by',
    ];

    // Database-generated helper columns used only for the "one primary image" unique indexes.
    protected $hidden = [
        'primary_destination_id',
        'primary_activity_id',
        'primary_event_id',
        'primary_experience_id',
    ];

    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
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

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}