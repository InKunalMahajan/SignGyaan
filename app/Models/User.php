<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'accessibility_preferences',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'accessibility_preferences' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    public function learningProgress(): HasMany
    {
        return $this->hasMany(LearningProgress::class);
    }

    public function learningActivities(): HasMany
    {
        return $this->hasMany(LearningActivity::class);
    }

    public function assessmentAttempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function accessibilityPreference(string $key, mixed $default = null): mixed
    {
        return data_get($this->accessibility_preferences ?? [], $key, $default);
    }

    public function notificationPreference(string $key, bool $default = true): bool
    {
        return (bool) data_get($this->notification_preferences ?? [], $key, $default);
    }

    public function wantsNotificationCategory(string $category): bool
    {
        if (! $this->notificationPreference('enabled', true)) {
            return false;
        }

        return $this->notificationPreference($category, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
