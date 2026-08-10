<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'avatar_path', 'timezone', 'locale', 'theme_preference'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    /**
     * Get the avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path) {
            return asset('avatars/' . $this->avatar_path);
        }
        return null;
    }

    /**
     * Get the default avatar URL.
     */
    public function getDefaultAvatarUrlAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0891b2&color=fff&size=128';
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail());
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification(mixed $token): void
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Get the is verified attribute.
     */
    protected function isVerified(): Attribute
    {
        return Attribute::make(
            get: fn () => ! is_null($this->email_verified_at),
        );
    }

    /**
     * Determine if the user is verified.
     */
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => now(),
        ])->save();
    }

    /**
     * Get the user's organization memberships.
     */
    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    /**
     * Get the user's organizations.
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

}
