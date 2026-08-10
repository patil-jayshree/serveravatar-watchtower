<?php

namespace App\Models;

use App\Enums\Project\ProjectEnvironment;
use App\Enums\Project\ProjectFramework;
use App\Enums\Project\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    #[Fillable(['name', 'description', 'framework', 'environment', 'status', 'organization_id'])]
    protected $fillable = [
        'name',
        'description',
        'framework',
        'environment',
        'status',
        'organization_id',
        'is_connected',
        'last_connected_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_connected' => 'boolean',
        'last_connected_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Project $project) {
            if (empty($project->uuid)) {
                $project->uuid = static::generateUniqueUuid();
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
     * Get the organization that owns the project.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who owns the project through the organization.
     */
    public function user(): BelongsTo
    {
        return $this->organization()->getRelation('user');
    }

    /**
     * Get the owner of the project (user who owns the organization).
     */
    public function owner(): BelongsTo
    {
        return $this->organization()->getRelation('user');
    }

    /**
     * Get the agent token for this project.
     */
    public function agentToken(): HasOne
    {
        return $this->hasOne(AgentToken::class);
    }

    /**
     * Check if a given user is the owner of this project.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->organization->user_id === $user->id;
    }

    /**
     * Get the framework enum.
     */
    public function getFrameworkEnumAttribute(): ?ProjectFramework
    {
        return ProjectFramework::tryFrom($this->framework);
    }

    /**
     * Get the environment enum.
     */
    public function getEnvironmentEnumAttribute(): ?ProjectEnvironment
    {
        return ProjectEnvironment::tryFrom($this->environment);
    }

    /**
     * Get the status enum.
     */
    public function getStatusEnumAttribute(): ?ProjectStatus
    {
        return ProjectStatus::tryFrom($this->status);
    }

    /**
     * Get the route key for the model (used for URL generation).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Mark the project as connected.
     */
    public function markAsConnected(): void
    {
        $this->update([
            'is_connected' => true,
            'last_connected_at' => now(),
        ]);
    }

    /**
     * Mark the project as disconnected.
     */
    public function markAsDisconnected(): void
    {
        $this->update([
            'is_connected' => false,
        ]);
    }
}
