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
 * @property string $command_name
 * @property string $status
 * @property int|null $exit_code
 * @property int|null $started_at
 * @property int|null $finished_at
 * @property float|null $duration_ms
 * @property array|null $arguments
 * @property array|null $options
 * @property string|null $request_id
 * @property string|null $exception_class
 * @property string|null $exception_message
 * @property string|null $exception_file
 * @property int|null $exception_line
 * @property string|null $stack_trace
 * @property string|null $environment
 * @property string|null $agent_version
 * @property string|null $laravel_version
 * @property string|null $php_version
 * @property string|null $server_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CommandEvent extends Model
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
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The name of the "updated at" column.
     */
    public const UPDATED_AT = 'updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'project_id',
        'command_name',
        'status',
        'exit_code',
        'started_at',
        'finished_at',
        'duration_ms',
        'arguments',
        'options',
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
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exit_code' => 'integer',
        'started_at' => 'integer',
        'finished_at' => 'integer',
        'duration_ms' => 'float',
        'exception_line' => 'integer',
        'arguments' => 'array',
        'options' => 'array',
    ];

    /**
     * Get the project that owns this command event.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the related request event if a request_id is set.
     */
    public function requestEvent(): BelongsTo
    {
        return $this->belongsTo(RequestEvent::class, 'request_id', 'request_id');
    }

    /**
     * Get the related exception occurrence.
     */
    public function exceptionOccurrence(): BelongsTo
    {
        return $this->belongsTo(ExceptionOccurrence::class, 'uuid', 'command_uuid');
    }

    /**
     * Check if the command completed successfully.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the command failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the command is slow (exceeds threshold).
     */
    public function isSlow(int $thresholdMs = 1000): bool
    {
        return $this->duration_ms !== null && $this->duration_ms >= $thresholdMs;
    }

    /**
     * Get the is_slow attribute.
     */
    public function getIsSlowAttribute(): bool
    {
        $thresholdMs = (int) config('watchtower.command_monitoring.slow_threshold_ms', 1000);
        return $this->isSlow($thresholdMs);
    }

    /**
     * Check if this command has an associated exception.
     */
    public function hasException(): bool
    {
        return $this->exception_class !== null;
    }

    /**
     * Get the duration formatted for display.
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
     * Get the started at timestamp.
     */
    public function getStartedAtFormattedAttribute(): ?string
    {
        if ($this->started_at === null) {
            return null;
        }

        return date('H:i:s', $this->started_at);
    }

    /**
     * Get the finished at timestamp.
     */
    public function getFinishedAtFormattedAttribute(): ?string
    {
        if ($this->finished_at === null) {
            return null;
        }

        return date('H:i:s', $this->finished_at);
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
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter by command name.
     */
    public function scopeCommand(Builder $query, string $commandName): Builder
    {
        return $query->where('command_name', $commandName);
    }

    /**
     * Scope: filter failed commands.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: filter slow commands (above threshold).
     */
    public function scopeSlow(Builder $query, int $thresholdMs = 1000): Builder
    {
        return $query->where('duration_ms', '>=', $thresholdMs);
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

    /**
     * Scope: search by command name.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where('command_name', 'LIKE', '%' . $search . '%');
    }

    /**
     * Get related exception group if this is a failed command.
     */
    public function getRelatedExceptionGroup(): ?ExceptionGroup
    {
        if (! $this->hasException()) {
            return null;
        }

        return ExceptionGroup::where('project_id', $this->project_id)
            ->where('exception_class', $this->exception_class)
            ->orderByDesc('last_seen_at')
            ->first();
    }

    /**
     * Get the source display string.
     */
    public function getSourceDisplayAttribute(): string
    {
        return 'Artisan Command';
    }
}
