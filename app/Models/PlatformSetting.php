<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $table = 'platform_settings';

    // The primary key is the string column "key", not "id".
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    // The table has updated_at only.
    public const CREATED_AT = null;

    protected $fillable = [
        'key',
        'value',
        'value_type',
        'description',
        'updated_by',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // The value is stored as text; this converts it according to value_type.
    public function typedValue(): string|int|float|bool
    {
        return match ($this->value_type) {
            'integer' => (int) $this->value,
            'decimal' => (float) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            default => (string) $this->value,
        };
    }
}