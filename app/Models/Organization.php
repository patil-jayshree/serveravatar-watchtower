<?php

namespace App\Models;

use App\Enums\OrganizationRole;
use App\Enums\OrganizationStatus;
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
    #[Fillable(['name', 'slug', 'logo_path', 'owner_id', 'status'])]
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'owner_id',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrganizationStatus::class,
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Organization $organization) {
            if (empty($organization->slug)) {
                $organization->slug = static::generateUniqueSlug($organization->name);
            }
        });

        static::updating(function (Organization $organization) {
            if ($organization->isDirty('name') && ! $organization->isDirty('slug')) {
                $organization->slug = static::generateUniqueSlug($organization->name);
            }
        });
    }

    /**
     * Generate a unique slug from the name.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the owner of the organization.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the organization memberships.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    /**
     * Get the organization members.
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class)->through('memberships');
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
     * Check if a user is a member of this organization.
     */
    public function hasMember(User $user): bool
    {
        return $this->memberships()->where('user_id', $user->id)->exists();
    }

    /**
     * Get a user's role in this organization.
     */
    public function getMemberRole(User $user): ?OrganizationRole
    {
        $membership = $this->memberships()->where('user_id', $user->id)->first();

        return $membership?->role;
    }

    /**
     * Check if a user has a specific role or higher.
     */
    public function userHasRole(User $user, OrganizationRole $role): bool
    {
        $userRole = $this->getMemberRole($user);

        if (! $userRole) {
            return false;
        }

        return match ($role) {
            OrganizationRole::Owner => $userRole === OrganizationRole::Owner,
            OrganizationRole::Admin => in_array($userRole, [OrganizationRole::Owner, OrganizationRole::Admin]),
            OrganizationRole::Member => true,
        };
    }

    /**
     * Get the member count.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->memberships()->count();
    }
}
