<?php

namespace App\Services;

use App\Models\Project;
use App\Models\RequestEvent;
use App\Models\QueryEvent;
use App\Models\JobEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    protected Project $project;
    protected string $timeRange;
    protected int $slowThreshold;
    protected string $from;
    protected string $to;

    public function __construct(Project $project, string $timeRange = '24h', ?int $slowThreshold = null)
    {
        $this->project = $project;
        $this->timeRange = $timeRange;
        $this->slowThreshold = $slowThreshold ?? (int) config('watchtower.slow_request_threshold', 1000);
        $this->from = $this->getFromDate();
        $this->to = now();
    }

    /**
     * Get the from date based on time range.
     */
    protected function getFromDate(): string
    {
        return match ($this->timeRange) {
            '1h' => now()->subHour()->toDateTimeString(),
            '24h' => now()->subDay()->toDateTimeString(),
            '7d' => now()->subWeek()->toDateTimeString(),
            '30d' => now()->subMonth()->toDateTimeString(),
            default => now()->subDay()->toDateTimeString(),
        };
    }

    /**
     * Get the time range label.
     */
    public function getTimeRangeLabel(): string
    {
        return match ($this->timeRange) {
            '1h' => 'Last 1 Hour',
            '24h' => 'Last 24 Hours',
            '7d' => 'Last 7 Days',
            '30d' => 'Last 30 Days',
            default => 'Last 24 Hours',
        };
    }

    /**
     * Get all performance metrics.
     */
    public function getMetrics(): array
    {
        return [
            'overview' => $this->getOverviewMetrics(),
            'response_time' => $this->getResponseTimeMetrics(),
            'throughput' => $this->getThroughputMetrics(),
            'error_rate' => $this->getErrorRateMetrics(),
            'slow_requests' => $this->getSlowRequestMetrics(),
            'memory' => $this->getMemoryMetrics(),
            'sql_contribution' => $this->getSqlContribution(),
        ];
    }

    /**
     * Get overview metrics combined.
     */
    public function getOverviewMetrics(): array
    {
        $metrics = $this->getThroughputMetrics();
        $responseTime = $this->getResponseTimeMetrics();
        $errorRate = $this->getErrorRateMetrics();
        $slowRequests = $this->getSlowRequestMetrics();

        return [
            'total_requests' => $metrics['total'],
            'requests_per_minute' => $metrics['requests_per_minute'],
            'avg_response_time_ms' => $responseTime['avg_ms'],
            'p95_response_time_ms' => $responseTime['p95_ms'],
            'error_rate' => $errorRate['error_rate'],
            'error_count' => $errorRate['error_count'],
            'slow_request_count' => $slowRequests['count'],
            'slow_request_rate' => $slowRequests['rate'],
        ];
    }

    /**
     * Get throughput metrics.
     */
    public function getThroughputMetrics(): array
    {
        $baseQuery = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from);

        $total = (clone $baseQuery)->count();

        // Calculate requests per minute based on time range
        $minutes = match ($this->timeRange) {
            '1h' => 60,
            '24h' => 1440,
            '7d' => 10080,
            '30d' => 43200,
            default => 1440,
        };

        $requestsPerMinute = $total > 0 ? round($total / max($minutes, 1), 1) : 0;

        return [
            'total' => $total,
            'requests_per_minute' => $requestsPerMinute,
        ];
    }

    /**
     * Get response time metrics.
     */
    public function getResponseTimeMetrics(): array
    {
        $query = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->whereNotNull('duration_ms');

        $count = (clone $query)->count();

        if ($count === 0) {
            return [
                'avg_ms' => null,
                'p50_ms' => null,
                'p95_ms' => null,
                'p99_ms' => null,
                'count' => 0,
            ];
        }

        // Calculate percentiles using subquery approach (compatible with MySQL 5.7+)
        $p50 = $this->calculatePercentile($query, 0.50);
        $p95 = $this->calculatePercentile($query, 0.95);
        $p99 = $this->calculatePercentile($query, 0.99);

        return [
            'avg_ms' => (clone $query)->avg('duration_ms') ? round((clone $query)->avg('duration_ms')) : null,
            'p50_ms' => $p50,
            'p95_ms' => $p95,
            'p99_ms' => $p99,
            'count' => $count,
        ];
    }

    /**
     * Calculate percentile using subquery (MySQL compatible).
     */
    protected function calculatePercentile($query, float $percentile): ?int
    {
        // Get the count first
        $count = (clone $query)->count();
        if ($count === 0) {
            return null;
        }

        $offset = (int) floor($count * $percentile);
        if ($offset >= $count) {
            $offset = $count - 1;
        }
        if ($offset < 0) {
            $offset = 0;
        }

        // Use Laravel's raw query with offset
        $result = DB::table(
            DB::raw("({$query->toSql()}) as subquery")
        )
            ->mergeBindings($query->getQuery())
            ->orderBy('duration_ms')
            ->offset($offset)
            ->limit(1)
            ->value('duration_ms');

        return $result ? (int) $result : null;
    }

    /**
     * Get error rate metrics.
     */
    public function getErrorRateMetrics(): array
    {
        $baseQuery = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from);

        $total = (clone $baseQuery)->count();
        $errors = (clone $baseQuery)->where('status_code', '>=', 500)->count();

        $errorRate = $total > 0 ? round(($errors / $total) * 100, 1) : 0;

        return [
            'error_rate' => $errorRate,
            'error_count' => $errors,
            'total_requests' => $total,
        ];
    }

    /**
     * Get slow request metrics.
     */
    public function getSlowRequestMetrics(): array
    {
        $baseQuery = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from);

        $total = (clone $baseQuery)->count();
        $slowCount = (clone $baseQuery)->where('duration_ms', '>=', $this->slowThreshold)->count();

        $slowRate = $total > 0 ? round(($slowCount / $total) * 100, 1) : 0;

        return [
            'count' => $slowCount,
            'rate' => $slowRate,
            'threshold_ms' => $this->slowThreshold,
        ];
    }

    /**
     * Get memory usage metrics.
     */
    public function getMemoryMetrics(): array
    {
        $query = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->whereNotNull('memory_bytes');

        $count = (clone $query)->count();

        if ($count === 0) {
            return [
                'has_data' => false,
                'avg_bytes' => null,
                'avg_mb' => null,
                'peak_bytes' => null,
                'peak_mb' => null,
            ];
        }

        $avgBytes = (clone $query)->avg('memory_bytes');
        $peakBytes = (clone $query)->max('memory_bytes');

        return [
            'has_data' => true,
            'avg_bytes' => $avgBytes ? (int) $avgBytes : null,
            'avg_mb' => $avgBytes ? round($avgBytes / 1024 / 1024, 1) : null,
            'peak_bytes' => $peakBytes ? (int) $peakBytes : null,
            'peak_mb' => $peakBytes ? round($peakBytes / 1024 / 1024, 1) : null,
        ];
    }

    /**
     * Get SQL contribution for the project.
     */
    public function getSqlContribution(): array
    {
        $queryQuery = QueryEvent::where('project_id', $this->project->id)
            ->where('occurred_at', '>=', $this->from);

        $totalSqlTime = (clone $queryQuery)->sum('duration_ms');
        $sqlQueryCount = (clone $queryQuery)->count();

        $requestQuery = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from);

        $totalRequestTime = (clone $requestQuery)->sum(DB::raw('COALESCE(duration_ms, 0)'));

        $sqlContribution = $totalRequestTime > 0
            ? round(($totalSqlTime / $totalRequestTime) * 100, 1)
            : 0;

        return [
            'has_data' => $sqlQueryCount > 0,
            'total_sql_time_ms' => (int) $totalSqlTime,
            'total_request_time_ms' => (int) $totalRequestTime,
            'sql_contribution_percent' => $sqlContribution,
            'query_count' => $sqlQueryCount,
        ];
    }

    /**
     * Get response time trend data.
     */
    public function getResponseTimeTrend(): array
    {
        $buckets = $this->getTimeBuckets();
        $groupBy = $this->getGroupByExpression();

        $trend = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->whereNotNull('duration_ms')
            ->select(
                DB::raw("{$groupBy} as bucket"),
                DB::raw('AVG(duration_ms) as avg_duration'),
                DB::raw('MAX(duration_ms) as max_duration'),
                DB::raw('COUNT(*) as request_count')
            )
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        return $this->fillTrendBuckets($buckets, $trend, 'avg_duration', 'max_duration');
    }

    /**
     * Get request throughput trend.
     */
    public function getThroughputTrend(): array
    {
        $buckets = $this->getTimeBuckets();
        $groupBy = $this->getGroupByExpression();

        $trend = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->select(
                DB::raw("{$groupBy} as bucket"),
                DB::raw('COUNT(*) as request_count')
            )
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        return $this->fillTrendBuckets($buckets, $trend, 'request_count');
    }

    /**
     * Get error trend.
     */
    public function getErrorTrend(): array
    {
        $buckets = $this->getTimeBuckets();
        $groupBy = $this->getGroupByExpression();

        $trend = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->where('status_code', '>=', 500)
            ->select(
                DB::raw("{$groupBy} as bucket"),
                DB::raw('COUNT(*) as error_count')
            )
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $bucketsFilled = $this->fillTrendBuckets($buckets, $trend, 'error_count');
        $totalErrors = collect($bucketsFilled)->sum('value');

        return [
            'buckets' => $bucketsFilled,
            'total_errors' => (int) $totalErrors,
        ];
    }

    /**
     * Get time bucket configuration based on range.
     */
    protected function getTimeBuckets(): array
    {
        $buckets = [];

        switch ($this->timeRange) {
            case '1h':
                // Minute buckets
                for ($i = 0; $i < 60; $i++) {
                    $buckets[] = now()->subHour()->startOfMinute()->addMinutes($i)->toDateTimeString();
                }
                break;
            case '24h':
                // Hourly buckets
                for ($i = 0; $i < 24; $i++) {
                    $buckets[] = now()->subDay()->startOfHour()->addHours($i)->toDateTimeString();
                }
                break;
            case '7d':
                // 6-hour buckets
                for ($i = 0; $i < 28; $i++) {
                    $buckets[] = now()->subWeek()->startOfHour()->addHours($i * 6)->toDateTimeString();
                }
                break;
            case '30d':
                // Daily buckets
                for ($i = 0; $i < 30; $i++) {
                    $buckets[] = now()->subMonth()->startOfDay()->addDays($i)->toDateTimeString();
                }
                break;
        }

        return $buckets;
    }

    /**
     * Get the GROUP BY expression for time bucketing.
     */
    protected function getGroupByExpression(): string
    {
        switch ($this->timeRange) {
            case '1h':
                return "DATE_FORMAT(requested_at, '%Y-%m-%d %H:%i:00')";
            case '24h':
                return "DATE_FORMAT(requested_at, '%Y-%m-%d %H:00:00')";
            case '7d':
                return "DATE_FORMAT(requested_at, '%Y-%m-%d %H:00:00')";
            case '30d':
                return "DATE_FORMAT(requested_at, '%Y-%m-%d 00:00:00')";
            default:
                return "DATE_FORMAT(requested_at, '%Y-%m-%d %H:00:00')";
        }
    }

    /**
     * Fill trend buckets with data, filling gaps with zeros.
     */
    protected function fillTrendBuckets(array $buckets, Collection $data, ...$valueFields): array
    {
        $dataMap = $data->keyBy('bucket')->map(function ($item) use ($valueFields) {
            $result = ['value' => $item->{$valueFields[0]} ?? 0];
            for ($i = 1; $i < count($valueFields); $i++) {
                $result[$valueFields[$i]] = $item->{$valueFields[$i]} ?? 0;
            }
            return $result;
        })->toArray();

        $filled = [];
        foreach ($buckets as $bucket) {
            if (isset($dataMap[$bucket])) {
                $filled[] = array_merge(['bucket' => $bucket], $dataMap[$bucket]);
            } else {
                $row = ['bucket' => $bucket, 'value' => 0];
                for ($i = 1; $i < count($valueFields); $i++) {
                    $row[$valueFields[$i]] = 0;
                }
                $filled[] = $row;
            }
        }

        return $filled;
    }

    /**
     * Get slowest endpoints.
     */
    public function getSlowestEndpoints(int $limit = 10): array
    {
        // Group by route name or normalized path
        $endpoints = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->whereNotNull('duration_ms')
            ->select([
                DB::raw($this->getEndpointGroupExpression() . ' as endpoint'),
                DB::raw('AVG(duration_ms) as avg_duration'),
                DB::raw('MAX(duration_ms) as max_duration'),
                DB::raw('COUNT(*) as request_count'),
            ])
            ->groupBy('endpoint')
            ->orderByRaw('AVG(duration_ms) DESC')
            ->limit($limit)
            ->get();

        // Calculate P95 for each endpoint
        return $endpoints->map(function ($endpoint) {
            $p95 = $this->getEndpointPercentile($endpoint->endpoint, 0.95);
            return [
                'endpoint' => $endpoint->endpoint,
                'avg_duration_ms' => (int) round($endpoint->avg_duration),
                'p95_duration_ms' => $p95,
                'max_duration_ms' => (int) $endpoint->max_duration,
                'request_count' => (int) $endpoint->request_count,
            ];
        })->toArray();
    }

    /**
     * Get endpoint grouping expression.
     */
    protected function getEndpointGroupExpression(): string
    {
        // Prefer route_name, fall back to method + path
        return "COALESCE(NULLIF(route_name, ''), CONCAT(method, ' ', path))";
    }

    /**
     * Get P95 for a specific endpoint.
     */
    protected function getEndpointPercentile(string $endpoint, float $percentile): ?int
    {
        // Use the same grouping expression as getSlowestEndpoints: include method when route_name is absent
        $count = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->whereRaw("COALESCE(NULLIF(route_name, ''), CONCAT(method, ' ', path)) = ?", [$endpoint])
            ->whereNotNull('duration_ms')
            ->count();

        if ($count === 0) {
            return null;
        }

        $offset = (int) floor($count * $percentile);
        if ($offset >= $count) {
            $offset = $count - 1;
        }

        $result = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->whereRaw("COALESCE(NULLIF(route_name, ''), CONCAT(method, ' ', path)) = ?", [$endpoint])
            ->whereNotNull('duration_ms')
            ->orderBy('duration_ms')
            ->offset($offset)
            ->limit(1)
            ->value('duration_ms');

        return $result ? (int) $result : null;
    }

    /**
     * Get recent slow requests.
     */
    public function getRecentSlowRequests(int $limit = 10): array
    {
        return RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->where('duration_ms', '>=', $this->slowThreshold)
            ->orderByDesc('duration_ms')
            ->limit($limit)
            ->get()
            ->map(function ($request) {
                return [
                    'uuid' => $request->uuid,
                    'method' => $request->method,
                    'path' => $request->path,
                    'route_name' => $request->route_name,
                    'duration_ms' => (int) $request->duration_ms,
                    'status_code' => $request->status_code,
                    'requested_at' => $request->requested_at->toIso8601String(),
                ];
            })
            ->toArray();
    }

    /**
     * Get jobs context for requests.
     */
    public function getJobsContext(): array
    {
        $recentRequestIds = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->pluck('request_id')
            ->filter()
            ->unique()
            ->take(1000);

        if ($recentRequestIds->isEmpty()) {
            return ['has_data' => false, 'dispatched_count' => 0, 'jobs' => []];
        }

        $jobs = JobEvent::where('project_id', $this->project->id)
            ->whereIn('request_id', $recentRequestIds)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return [
            'has_data' => $jobs->isNotEmpty(),
            'dispatched_count' => $jobs->count(),
            'jobs' => $jobs->map(function ($job) {
                return [
                    'uuid' => $job->uuid,
                    'job_name' => $job->job_name,
                    'status' => $job->status,
                    'duration_ms' => $job->duration_ms,
                    'created_at' => $job->created_at->toIso8601String(),
                ];
            })->toArray(),
        ];
    }

    /**
     * Check if project has any performance data.
     */
    public function hasData(): bool
    {
        return RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->exists();
    }
}
