<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Governorate extends Model
{
    use HasFactory;

    protected $table = 'governorates';

    protected $fillable = [
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
        return $this->hasMany(Destination::class, 'governorate_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'governorate_id');
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'governorate_id');
    }
}