<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RefreshToken extends Model
{
    use HasFactory;

    protected $table = 'refresh_tokens';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'created_by_ip',
        'revoked_at',
        'revoked_by_ip',
        'replaced_by_token_id',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaced_by_token_id');
    }

    public function replaces(): HasOne
    {
        return $this->hasOne(self::class, 'replaced_by_token_id');
    }
}