<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExceptionGroup extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'uuid';

    /**
     * The primary key type.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'exception_type',
        'group_signature',
        'normalized_message',
        'file',
        'line',
        'first_seen_at',
        'last_seen_at',
        'occurrence_count',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'line' => 'integer',
        'occurrence_count' => 'integer',
    ];

    /**
     * Status constants.
     */
    public const STATUS_OPEN = 'open';
    public const STATUS_RESOLVED = 'resolved';

    /**
     * Get the project that owns the exception group.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the occurrences for this exception group.
     */
    public function occurrences(): HasMany
    {
        return $this->hasMany(ExceptionOccurrence::class, 'exception_group_uuid');
    }

    /**
     * Get the latest occurrence.
     */
    public function latestOccurrence(): HasOne
    {
        return $this->hasOne(ExceptionOccurrence::class, 'exception_group_uuid')
            ->latestOfMany('occurred_at');
    }

    /**
     * Check if the group is open.
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if the group is resolved.
     */
    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    /**
     * Mark the group as resolved.
     */
    public function markAsResolved(): void
    {
        $this->update(['status' => self::STATUS_RESOLVED]);
    }

    /**
     * Mark the group as open.
     */
    public function markAsOpen(): void
    {
        $this->update(['status' => self::STATUS_OPEN]);
    }

    /**
     * Reopen the group if it was resolved.
     */
    public function reopenIfResolved(): void
    {
        if ($this->isResolved()) {
            $this->markAsOpen();
        }
    }

    /**
     * Increment occurrence count and update last_seen_at.
     */
    public function recordOccurrence(?\DateTimeInterface $occurredAt = null): void
    {
        $this->increment('occurrence_count');
        $this->update(['last_seen_at' => $occurredAt ?? now()]);
    }

    /**
     * Scope: filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter by project.
     */
    public function scopeForProject($query, Project $project)
    {
        return $query->where('project_id', $project->id);
    }

    /**
     * Scope: open groups only.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope: resolved groups only.
     */
    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }
}
