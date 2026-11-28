<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\Role;


class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens, MustVerifyEmail;

    protected $table = 'users';

    protected static function booted(): void
    {
        static::updated(function (User $user): void {
            if ($user->wasChanged('is_active') && ! $user->is_active) {
                $user->tokens()->delete();
            }
        });
    }

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
        'role',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
        'failed_login_attempts',
        'last_login_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'failed_login_attempts' => 'integer',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'role' => Role::class,
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
