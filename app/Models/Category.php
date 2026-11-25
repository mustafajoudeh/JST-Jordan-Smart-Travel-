<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'kind',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class, 'category_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'category_id');
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'category_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    public function interestedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_interests', 'category_id', 'user_id')
            ->using(UserInterest::class)
            ->withPivot('created_at');
    }
}