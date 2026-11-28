<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserInterest extends Pivot
{
    use HasFactory;

    protected $table = 'user_interests';

    public $timestamps = false;

    // Composite PK (user_id, category_id): Eloquent cannot use it as a model key,
    // so find()/update()/delete() on a single instance are not supported.
    // Use the BelongsToMany relations (attach/detach/sync) or the query builder.
    public $incrementing = false;

    public const UPDATED_AT = null;

    public function getUpdatedAtColumn()
    {
        return null;
    }

    protected $fillable = [
        'user_id',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
