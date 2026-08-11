<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class LogEvent extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'log_events';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'project_id',
        'level',
        'message',
        'context',
        'channel',
        'request_id',
        'exception_class',
        'exception_message',
        'file',
        'line',
        'environment',
        'host',
        'agent_version',
        'logged_at',
    ];

    protected $casts = [
        'context' => 'array',
        'line' => 'integer',
        'logged_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Log levels with severity ranking
    public const LEVEL_DEBUG = 'DEBUG';
    public const LEVEL_INFO = 'INFO';
    public const LEVEL_NOTICE = 'NOTICE';
    public const LEVEL_WARNING = 'WARNING';
    public const LEVEL_ERROR = 'ERROR';
    public const LEVEL_CRITICAL = 'CRITICAL';
    public const LEVEL_ALERT = 'ALERT';
    public const LEVEL_EMERGENCY = 'EMERGENCY';

    public const LEVELS = [
        self::LEVEL_DEBUG,
        self::LEVEL_INFO,
        self::LEVEL_NOTICE,
        self::LEVEL_WARNING,
        self::LEVEL_ERROR,
        self::LEVEL_CRITICAL,
        self::LEVEL_ALERT,
        self::LEVEL_EMERGENCY,
    ];

    public const LEVEL_RANK = [
        self::LEVEL_DEBUG => 0,
        self::LEVEL_INFO => 1,
        self::LEVEL_NOTICE => 2,
        self::LEVEL_WARNING => 3,
        self::LEVEL_ERROR => 4,
        self::LEVEL_CRITICAL => 5,
        self::LEVEL_ALERT => 6,
        self::LEVEL_EMERGENCY => 7,
    ];

    /**
     * Get the project that owns this log event.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the related request event if available.
     */
    public function requestEvent(): BelongsTo
    {
        return $this->belongsTo(RequestEvent::class, 'request_id', 'request_id');
    }

    /**
     * Get the related exception group if available.
     */
    public function getRelatedExceptionGroup(): ?ExceptionGroup
    {
        if (!$this->exception_class) {
            return null;
        }

        return ExceptionGroup::where('project_id', $this->project_id)
            ->where('exception_type', $this->exception_class)
            ->where('normalized_message', $this->exception_message)
            ->orderByDesc('last_seen_at')
            ->first();
    }

    /**
     * Scope to filter by project.
     */
    public function scopeForProject(Builder $query, Project $project): Builder
    {
        return $query->where('project_id', $project->id);
    }

    /**
     * Scope to filter by level.
     */
    public function scopeForLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', strtoupper($level));
    }

    /**
     * Scope to filter by channel.
     */
    public function scopeForChannel(Builder $query, string $channel): Builder
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope to filter by request ID.
     */
    public function scopeForRequest(Builder $query, string $requestId): Builder
    {
        return $query->where('request_id', $requestId);
    }

    /**
     * Scope to filter by environment.
     */
    public function scopeForEnvironment(Builder $query, string $environment): Builder
    {
        return $query->where('environment', $environment);
    }

    /**
     * Scope to filter by time range.
     */
    public function scopeInTimeRange(Builder $query, ?string $timeRange): Builder
    {
        if (!$timeRange || $timeRange === 'all') {
            return $query;
        }

        $now = now();

        return match ($timeRange) {
            '24h' => $query->where('logged_at', '>=', $now->copy()->subDay()),
            '7d' => $query->where('logged_at', '>=', $now->copy()->subDays(7)),
            '30d' => $query->where('logged_at', '>=', $now->copy()->subDays(30)),
            default => $query,
        };
    }

    /**
     * Scope to search in message.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        $searchTerm = '%' . $search . '%';

        return $query->where(function (Builder $q) use ($searchTerm) {
            $q->where('message', 'LIKE', $searchTerm)
              ->orWhere('exception_class', 'LIKE', $searchTerm)
              ->orWhere('exception_message', 'LIKE', $searchTerm)
              ->orWhere('channel', 'LIKE', $searchTerm);
        });
    }

    /**
     * Scope to filter error-level logs.
     */
    public function scopeErrors(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('level', self::LEVEL_ERROR)
              ->orWhere('level', self::LEVEL_CRITICAL)
              ->orWhere('level', self::LEVEL_ALERT)
              ->orWhere('level', self::LEVEL_EMERGENCY);
        });
    }

    /**
     * Scope to filter warning-level logs.
     */
    public function scopeWarnings(Builder $query): Builder
    {
        return $query->where('level', self::LEVEL_WARNING);
    }

    /**
     * Check if this log level is error or higher.
     */
    public function isErrorLevel(): bool
    {
        $rank = self::LEVEL_RANK[$this->level] ?? 0;
        return $rank >= (self::LEVEL_RANK[self::LEVEL_ERROR] ?? 4);
    }

    /**
     * Get a CSS class for the log level badge.
     */
    public function getLevelBadgeClass(): string
    {
        return match ($this->level) {
            self::LEVEL_DEBUG => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            self::LEVEL_INFO => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            self::LEVEL_NOTICE => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
            self::LEVEL_WARNING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            self::LEVEL_ERROR => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            self::LEVEL_CRITICAL => 'bg-red-200 text-red-900 dark:bg-red-900/50 dark:text-red-300',
            self::LEVEL_ALERT => 'bg-orange-200 text-orange-900 dark:bg-orange-900/50 dark:text-orange-300',
            self::LEVEL_EMERGENCY => 'bg-purple-200 text-purple-900 dark:bg-purple-900/50 dark:text-purple-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }

    /**
     * Get formatted logged_at timestamp.
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->logged_at?->format('M j, Y H:i:s') ?? '';
    }
}
