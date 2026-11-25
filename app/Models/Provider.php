<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    use HasFactory;

    protected $table = 'providers';

    // Admin/financial fields (status, verification, review, commission,
    // booking permission, payout) are intentionally not mass assignable.
    protected $fillable = [
        'user_id',
        'provider_type',
        'display_name',
        'description',
        'contact_email',
        'contact_phone',
        'website_url',
        'address',
        'governorate_id',
        'category_id',
        'languages',
        'specializations',
    ];

    protected $hidden = [
        'payout_account',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'specializations' => 'array',
            'reviewed_at' => 'datetime',
            'commission_rate' => 'decimal:4',
            'can_receive_bookings' => 'boolean',
            'payout_account' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_id');
    }
}