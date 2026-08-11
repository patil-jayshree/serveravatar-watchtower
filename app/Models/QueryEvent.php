<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueryEvent extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Query type constants.
     */
    public const TYPE_SELECT = 'select';
    public const TYPE_INSERT = 'insert';
    public const TYPE_UPDATE = 'update';
    public const TYPE_DELETE = 'delete';
    public const TYPE_OTHER = 'other';

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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'request_id',
        'sql',
        'normalized_sql',
        'bindings',
        'duration_ms',
        'connection_name',
        'driver',
        'database_name',
        'query_type',
        'is_slow',
        'transaction_id',
        'occurred_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bindings' => 'array',
        'duration_ms' => 'integer',
        'is_slow' => 'boolean',
        'occurred_at' => 'datetime',
    ];

    /**
     * Get the project that owns this query event.
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
     * Scope: filter by project.
     */
    public function scopeForProject($query, Project $project)
    {
        return $query->where('project_id', $project->id);
    }

    /**
     * Scope: filter slow queries.
     */
    public function scopeSlow($query)
    {
        return $query->where('is_slow', true);
    }

    /**
     * Scope: filter by query type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('query_type', $type);
    }

    /**
     * Scope: filter by connection.
     */
    public function scopeOnConnection($query, string $connection)
    {
        return $query->where('connection_name', $connection);
    }

    /**
     * Scope: filter by time range.
     */
    public function scopeInTimeRange($query, string $range)
    {
        return match ($range) {
            '24h' => $query->where('occurred_at', '>=', now()->subDay()),
            '7d' => $query->where('occurred_at', '>=', now()->subWeek()),
            '30d' => $query->where('occurred_at', '>=', now()->subMonth()),
            default => $query,
        };
    }

    /**
     * Scope: search in normalized SQL.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('normalized_sql', 'like', "%{$search}%");
    }

    /**
     * Detect query type from SQL statement.
     */
    public static function detectQueryType(string $sql): string
    {
        $sql = trim($sql);
        $firstWord = strtolower(strtok($sql, " \t\n\r"));

        return match ($firstWord) {
            'select' => self::TYPE_SELECT,
            'insert' => self::TYPE_INSERT,
            'update' => self::TYPE_UPDATE,
            'delete' => self::TYPE_DELETE,
            default => self::TYPE_OTHER,
        };
    }

    /**
     * Normalize SQL for grouping similar queries.
     *
     * Replaces numeric values, quoted strings, and IN() lists with placeholders.
     */
    public static function normalizeSql(string $sql): string
    {
        // Trim and normalize whitespace
        $normalized = preg_replace('/\s+/', ' ', trim($sql));

        // Replace quoted strings: 'value' -> '?'
        $normalized = preg_replace("/'[^']*'/", "'?'", $normalized);

        // Replace numeric values: 123 -> ?
        $normalized = preg_replace('/\b\d+(\.\d+)?\b/', '?', $normalized);

        // Replace IN(...) lists with IN(?)
        $normalized = preg_replace('/IN\s*\(\s*(?:\?\s*,\s*)+\s*\)/i', 'IN (?)', $normalized);
        $normalized = preg_replace('/IN\s*\(\s*(?:\d+\s*,\s*)+\s*\)/i', 'IN (?)', $normalized);

        // Truncate if too long
        if (strlen($normalized) > 1000) {
            $normalized = substr($normalized, 0, 1000);
        }

        return $normalized;
    }

    /**
     * Check if this is a slow query based on threshold.
     */
    public function isSlow(int $thresholdMs = 500): bool
    {
        return $this->duration_ms >= $thresholdMs;
    }

    /**
     * Get a short preview of the SQL.
     */
    public function getSqlPreviewAttribute(): string
    {
        $sql = $this->sql;

        if (strlen($sql) > 150) {
            return substr($sql, 0, 150) . '...';
        }

        return $sql;
    }
}
