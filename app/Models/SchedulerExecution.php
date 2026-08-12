<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property string $uuid
 * @property int $project_id
 * @property string $scheduler_task_uuid
 * @property string $status
 * @property \Carbon\Carbon|null $expected_at
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $finished_at
 * @property int|null $duration_ms
 * @property int|null $delay_ms
 * @property string|null $command_name
 * @property string|null $command_uuid
 * @property string|null $job_name
 * @property string|null $job_uuid
 * @property string|null $exception_uuid
 * @property string|null $exception_class
 * @property string|null $exception_message
 * @property string|null $stack_trace
 * @property string|null $environment
 * @property string|null $agent_version
 * @property string|null $laravel_version
 * @property string|null $php_version
 * @property string|null $server_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SchedulerExecution extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'project_id',
        'scheduler_task_uuid',
        'status',
        'expected_at',
        'started_at',
        'finished_at',
        'duration_ms',
        'delay_ms',
        'command_name',
        'command_uuid',
        'job_name',
        'job_uuid',
        'exception_uuid',
        'exception_class',
        'exception_message',
        'stack_trace',
        'environment',
        'agent_version',
        'laravel_version',
        'php_version',
        'server_name',
    ];

    protected $casts = [
        'expected_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration_ms' => 'integer',
        'delay_ms' => 'integer',
    ];

    /**
     * Get the scheduler task that owns this execution.
     */
    public function schedulerTask(): BelongsTo
    {
        return $this->belongsTo(SchedulerTask::class, 'scheduler_task_uuid');
    }

    /**
     * Get the project that owns this execution.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the linked command event.
     */
    public function commandEvent(): ?CommandEvent
    {
        if (! $this->command_uuid) {
            return null;
        }

        return CommandEvent::where('uuid', $this->command_uuid)
            ->where('project_id', $this->project_id)
            ->first();
    }

    /**
     * Get the linked job event.
     */
    public function jobEvent(): ?JobEvent
    {
        if (! $this->job_uuid) {
            return null;
        }

        return JobEvent::where('uuid', $this->job_uuid)
            ->where('project_id', $this->project_id)
            ->first();
    }

    /**
     * Get the linked exception occurrence.
     */
    public function exceptionOccurrence(): ?ExceptionOccurrence
    {
        if (! $this->exception_uuid) {
            return null;
        }

        return ExceptionOccurrence::where('uuid', $this->exception_uuid)
            ->where('project_id', $this->project_id)
            ->first();
    }

    /**
     * Get duration formatted for display.
     */
    public function getDurationFormattedAttribute(): string
    {
        if ($this->duration_ms === null) {
            return '—';
        }

        if ($this->duration_ms < 1000) {
            return round($this->duration_ms) . ' ms';
        }

        return round($this->duration_ms / 1000, 1) . ' sec';
    }

    /**
     * Get delay formatted for display.
     */
    public function getDelayFormattedAttribute(): string
    {
        if ($this->delay_ms === null || $this->delay_ms <= 0) {
            return '—';
        }

        $seconds = round($this->delay_ms / 1000);
        if ($seconds < 60) {
            return $seconds . ' sec';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($remainingSeconds === 0) {
            return $minutes . ' min';
        }

        return $minutes . ' min ' . $remainingSeconds . ' sec';
    }

    /**
     * Get the status display.
     */
    public function getStatusDisplayAttribute(): array
    {
        return match ($this->status) {
            'started' => ['label' => 'Running', 'color' => 'blue'],
            'completed' => ['label' => 'Completed', 'color' => 'green'],
            'failed' => ['label' => 'Failed', 'color' => 'red'],
            'missed' => ['label' => 'Missed', 'color' => 'yellow'],
            default => ['label' => ucfirst($this->status), 'color' => 'gray'],
        };
    }

    /**
     * Check if the execution is healthy (completed on time or early).
     */
    public function isHealthy(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the execution failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the execution was missed.
     */
    public function isMissed(): bool
    {
        return $this->status === 'missed';
    }

    /**
     * Check if the execution is running.
     */
    public function isRunning(): bool
    {
        return $this->status === 'started';
    }

    /**
     * Scope: filter by project.
     */
    public function scopeForProject(Builder $query, Project $project): Builder
    {
        return $query->where('project_id', $project->id);
    }

    /**
     * Scope: filter by task.
     */
    public function scopeForTask(Builder $query, SchedulerTask $task): Builder
    {
        return $query->where('scheduler_task_uuid', $task->uuid);
    }

    /**
     * Scope: filter by status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter by time range.
     */
    public function scopeInTimeRange(Builder $query, string $range): Builder
    {
        $from = match ($range) {
            '1h' => now()->subHour(),
            '24h' => now()->subDay(),
            '7d' => now()->subWeek(),
            '30d' => now()->subMonth(),
            default => now()->subDay(),
        };

        return $query->where('created_at', '>=', $from);
    }
}
