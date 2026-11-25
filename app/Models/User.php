<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password_hash',
        'country_code',
        'photo_url',
        'phone',
        'budget_level',
        'trip_pace',
        'group_type',
        'activity_level',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'failed_login_attempts' => 'integer',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

   public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function refreshTokens(): HasMany
    {
        return $this->hasMany(RefreshToken::class, 'user_id');
    }

    public function createdDestinations(): HasMany
    {
        return $this->hasMany(Destination::class, 'created_by');
    }

    public function createdActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'user_interests', 'user_id', 'category_id')
            ->using(UserInterest::class)
            ->withPivot('created_at');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'user_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'user_id');
    }

    public function moderatedExperiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'moderated_by');
    }

    public function uploadedMedia(): HasMany
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    public function provider(): HasOne
    {
        return $this->hasOne(Provider::class, 'user_id');
    }

    public function reviewedProviders(): HasMany
    {
        return $this->hasMany(Provider::class, 'reviewed_by');
    }

    public function reviewedServices(): HasMany
    {
        return $this->hasMany(Service::class, 'reviewed_by');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function cancelledBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'cancelled_by');
    }

    public function initiatedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'initiated_by');
    }

    public function updatedSettings(): HasMany
    {
        return $this->hasMany(PlatformSetting::class, 'updated_by');
    }
}