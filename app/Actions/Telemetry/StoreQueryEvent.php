<?php

declare(strict_types=1);

namespace App\Actions\Telemetry;

use App\Models\Project;
use App\Models\QueryEvent;

class StoreQueryEvent
{
    /**
     * Default slow query threshold in milliseconds.
     */
    protected int $slowQueryThreshold;

    public function __construct(int $slowQueryThreshold = 500)
    {
        $this->slowQueryThreshold = $slowQueryThreshold;
    }

    /**
     * Store a query event from the agent.
     */
    public function execute(Project $project, array $data): QueryEvent
    {
        $sql = $data['sql'];
        $normalizedSql = QueryEvent::normalizeSql($sql);
        $queryType = QueryEvent::detectQueryType($sql);
        $durationMs = (int) $data['duration_ms'];
        $isSlow = $durationMs >= $this->slowQueryThreshold;

        return QueryEvent::create([
            'project_id' => $project->id,
            'request_id' => $data['request_id'] ?? null,
            'sql' => $sql,
            'normalized_sql' => $normalizedSql,
            'bindings' => $data['bindings'] ?? null,
            'duration_ms' => $durationMs,
            'connection_name' => $data['connection_name'] ?? null,
            'driver' => $data['driver'] ?? null,
            'database_name' => $data['database_name'] ?? null,
            'query_type' => $queryType,
            'is_slow' => $isSlow,
            'transaction_id' => $data['transaction_id'] ?? null,
            'occurred_at' => $data['occurred_at'] ?? now(),
        ]);
    }

    /**
     * Set the slow query threshold.
     */
    public function setSlowQueryThreshold(int $thresholdMs): void
    {
        $this->slowQueryThreshold = $thresholdMs;
    }
}
