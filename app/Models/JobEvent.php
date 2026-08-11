<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobEvent extends Model
{
    use HasFactory, HasUuids;

    /**
     * Boot the model to ensure UUID is set.
     */
    /**
     * The primary key.
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
     * Status constants.
     */
    public const STATUS_QUEUED = 'queued';
    public const STATUS_STARTED = 'started';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'project_id',
        'job_id',
        'job_uuid',
        'job_name',
        'queue',
        'connection',
        'status',
        'attempts',
        'queued_at',
        'started_at',
        'completed_at',
        'failed_at',
        'duration_ms',
        'request_id',
        'exception_class',
        'exception_message',
        'exception_file',
        'exception_line',
        'stack_trace',
        'environment',
        'agent_version',
        'laravel_version',
        'php_version',
        'server_name',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'attempts' => 'integer',
        'queued_at' => 'integer',
        'started_at' => 'integer',
        'completed_at' => 'integer',
        'failed_at' => 'integer',
        'duration_ms' => 'float',
        'exception_line' => 'integer',
    ];

    /**
     * Get the project that owns the job event.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the related request event if available.
     */
    public function getRelatedRequestEvent(): ?RequestEvent
    {
        if (! $this->request_id) {
            return null;
        }

        return RequestEvent::where('request_id', $this->request_id)
            ->where('project_id', $this->project_id)
            ->first();
    }

    /**
     * Get the related exception group for failed jobs.
     *
     * Finds the exception occurrence with matching job_uuid, then returns its group.
     * This ensures we get the EXACT exception caused by this specific job, not just
     * any exception with the same exception type.
     */
    public function getRelatedExceptionGroup(): ?\App\Models\ExceptionGroup
    {
        if (! $this->isFailed()) {
            return null;
        }

        // Find the exception occurrence created by this job (via job_uuid)
        $occurrence = \App\Models\ExceptionOccurrence::where('job_uuid', $this->uuid)
            ->where('project_id', $this->project_id)
            ->first();

        if (! $occurrence) {
            return null;
        }

        return $occurrence->exceptionGroup;
    }

    /**
     * Get the display name of the job.
     */
    public function getDisplayNameAttribute(): string
    {
        return class_basename($this->job_name);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_ms === null) {
            return '—';
        }

        if ($this->duration_ms < 1000) {
            return round($this->duration_ms) . ' ms';
        }

        return round($this->duration_ms / 1000, 2) . ' sec';
    }

    /**
     * Check if the job failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if the job completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the job is running.
     */
    public function isRunning(): bool
    {
        return $this->status === self::STATUS_STARTED;
    }

    /**
     * Check if the job is pending/queued.
     */
    public function isQueued(): bool
    {
        return $this->status === self::STATUS_QUEUED;
    }

    /**
     * Scope a query to filter by project.
     */
    public function scopeForProject($query, Project $project)
    {
        return $query->where('project_id', $project->id);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by queue.
     */
    public function scopeOnQueue($query, string $queue)
    {
        return $query->where('queue', $queue);
    }

    /**
     * Scope a query to filter by connection.
     */
    public function scopeOnConnection($query, string $connection)
    {
        return $query->where('connection', $connection);
    }

    /**
     * Scope a query to filter by job name search.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('job_name', 'like', "%{$search}%");
    }

    /**
     * Scope a query to filter within a time range.
     */
    public function scopeInTimeRange($query, string $range)
    {
        $cutoffs = [
            '24h' => now()->subHours(24)->timestamp,
            '7d' => now()->subDays(7)->timestamp,
            '30d' => now()->subDays(30)->timestamp,
        ];

        if (isset($cutoffs[$range])) {
            return $query->where('queued_at', '>=', $cutoffs[$range]);
        }

        return $query;
    }

    /**
     * Scope a query to only failed jobs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope a query to only slow jobs.
     */
    public function scopeSlow($query, int $thresholdMs = 1000)
    {
        return $query->where('duration_ms', '>=', $thresholdMs);
    }
}
