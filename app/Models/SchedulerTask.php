<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property string $uuid
 * @property int $project_id
 * @property string $task_name
 * @property string $task_type
 * @property string|null $command_name
 * @property string|null $job_name
 * @property string|null $job_uuid
 * @property string|null $expression
 * @property string|null $description
 * @property string|null $timezone
 * @property string|null $environment
 * @property \Carbon\Carbon|null $next_run_at
 * @property \Carbon\Carbon|null $last_run_at
 * @property string|null $last_status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SchedulerTask extends Model
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
        'task_name',
        'task_type',
        'command_name',
        'job_name',
        'job_uuid',
        'expression',
        'description',
        'timezone',
        'environment',
        'next_run_at',
        'last_run_at',
        'last_status',
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    /**
     * Get the project that owns this scheduler task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the executions for this task.
     */
    public function executions(): HasMany
    {
        return $this->hasMany(SchedulerExecution::class, 'scheduler_task_uuid');
    }

    /**
     * Get the latest execution.
     */
    public function getLatestExecutionAttribute(): ?SchedulerExecution
    {
        return $this->executions()->latest('created_at')->first();
    }

    /**
     * Get the linked command event if task_type is 'command'.
     */
    public function commandEvent(): ?CommandEvent
    {
        if (! $this->command_name) {
            return null;
        }

        return CommandEvent::where('project_id', $this->project_id)
            ->where('command_name', $this->command_name)
            ->latest('created_at')
            ->first();
    }

    /**
     * Get the linked job event if task_type is 'job'.
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
     * Get the human-readable frequency from the cron expression.
     */
    public function getFrequencyAttribute(): string
    {
        $expr = $this->expression;
        if (! $expr) {
            return $this->description ?? 'Unknown';
        }

        // Parse the cron expression
        $parts = preg_split('/\s+/', trim($expr));
        if (count($parts) !== 5) {
            return $this->description ?? $expr;
        }

        [$min, $hour, $day, $mon, $dow] = $parts;

        // Every minute: * * * * *
        if ($min === '*' && $hour === '*' && $day === '*' && $mon === '*' && $dow === '*') {
            return 'Every minute';
        }

        // Every N minutes: */N * * * *
        if (preg_match('/^\*\/(\d+)$/', $min, $m) && $hour === '*' && $day === '*' && $mon === '*' && $dow === '*') {
            $n = (int) $m[1];
            if ($n === 1) return 'Every minute';
            if ($n === 2) return 'Every 2 minutes';
            if ($n === 3) return 'Every 3 minutes';
            if ($n === 4) return 'Every 4 minutes';
            if ($n === 5) return 'Every 5 minutes';
            if ($n === 10) return 'Every 10 minutes';
            if ($n === 15) return 'Every 15 minutes';
            if ($n === 20) return 'Every 20 minutes';
            if ($n === 30) return 'Every 30 minutes';
            return "Every {$n} minutes";
        }

        // Every hour at minute N: N * * * *
        if ($hour === '*' && $day === '*' && $mon === '*' && $dow === '*' && preg_match('/^\d+$/', $min)) {
            $n = (int) $min;
            if ($n === 0) return 'Every hour';
            return "Every hour at minute {$n}";
        }

        // Daily at specific time: H M * * *
        if ($day === '*' && $mon === '*' && $dow === '*' && preg_match('/^\d+$/', $min) && preg_match('/^\d+$/', $hour)) {
            return sprintf('Daily at %02d:%02d', (int) $hour, (int) $min);
        }

        // Every weekday at specific time: * * * * 1-5
        if ($min !== '*' && $hour !== '*' && $day === '*' && $mon === '*' && $dow === '1-5') {
            return sprintf('Weekdays at %02d:%02d', (int) $hour, (int) $min);
        }

        // Weekly (Sunday) at time: * * * * 0
        if ($min !== '*' && $hour !== '*' && $day === '*' && $mon === '*' && $dow === '0') {
            return sprintf('Weekly (Sunday) at %02d:%02d', (int) $hour, (int) $min);
        }

        // Weekly at specific time: * * * * N
        if ($min !== '*' && $hour !== '*' && $day === '*' && $mon === '*' && preg_match('/^[1-7]$/', $dow)) {
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $dayName = $days[(int) $dow - 1] ?? "Day {$dow}";
            return sprintf('%s at %02d:%02d', $dayName, (int) $hour, (int) $min);
        }

        // Monthly: * * N * *
        if ($min !== '*' && $hour !== '*' && preg_match('/^\d+$/', $day) && $day !== '*') {
            return sprintf('Monthly on day %s at %02d:%02d', $day, (int) $hour, (int) $min);
        }

        // Fallback to description or raw expression
        return $this->description ?? $expr;
    }

    /**
     * Get the status display with color.
     */
    public function getStatusDisplayAttribute(): array
    {
        return match ($this->last_status) {
            'completed' => ['label' => 'Healthy', 'color' => 'green'],
            'healthy' => ['label' => 'Healthy', 'color' => 'green'],
            'running' => ['label' => 'Running', 'color' => 'blue'],
            'failed' => ['label' => 'Failed', 'color' => 'red'],
            'missed' => ['label' => 'Missed', 'color' => 'yellow'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }

    /**
     * Scope: filter by project.
     */
    public function scopeForProject(Builder $query, Project $project): Builder
    {
        return $query->where('project_id', $project->id);
    }

    /**
     * Scope: filter by status.
     * Maps UI status labels to stored last_status values.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        // Map UI display status to stored last_status value
        $storedStatus = match ($status) {
            'healthy' => 'completed',
            'running', 'failed', 'missed' => $status,
            default => $status,
        };

        return $query->where('last_status', $storedStatus);
    }

    /**
     * Scope: filter by environment.
     */
    public function scopeEnvironment(Builder $query, string $environment): Builder
    {
        return $query->where('environment', $environment);
    }

    /**
     * Scope: search by task name.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where('task_name', 'LIKE', '%' . $search . '%');
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

        return $query->where('updated_at', '>=', $from);
    }
}
