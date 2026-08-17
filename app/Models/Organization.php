<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    #[Fillable(['name', 'logo_path', 'description', 'user_id'])]
    protected $fillable = [
        'name',
        'logo_path',
        'description',
        'user_id',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Organization $organization) {
            if (empty($organization->uuid)) {
                $organization->uuid = static::generateUniqueUuid();
            }
        });
    }

    /**
     * Generate a unique public UUID.
     */
    public static function generateUniqueUuid(): string
    {
        do {
            $uuid = Str::uuid()->toString();
        } while (static::where('uuid', $uuid)->exists());

        return $uuid;
    }

    /**
     * Get the owner of the organization.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user() - the owner of the organization.
     */
    public function owner(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Get the projects belonging to this organization.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path) {
            return asset('avatars/' . $this->logo_path);
        }

        return null;
    }

    /**
     * Get the default logo URL.
     */
    public function getDefaultLogoUrlAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0891b2&color=fff&size=128';
    }

    /**
     * Check if a given user is the owner of this organization.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
