<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Models\RequestEvent;
use App\Models\ExceptionGroup;
use App\Models\ExceptionOccurrence;
use App\Models\QueryEvent;
use App\Models\JobEvent;
use App\Models\CommandEvent;
use App\Models\SchedulerTask;
use App\Models\SchedulerExecution;
use Illuminate\Support\Facades\DB;

class ProjectOverviewService
{
    protected Project $project;
    protected string $timeRange;
    protected string $from;

    public function __construct(Project $project, string $timeRange = '24h')
    {
        $this->project = $project;
        $this->timeRange = $timeRange;
        $this->from = $this->getFromDate();
    }

    public function getFrom(): string
    {
        return $this->from;
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
     * Get all overview data in one call.
     */
    public function getOverview(): array
    {
        return [
            'header' => $this->getHeader(),
            'health' => $this->getHealth(),
            'requests' => $this->getRequestOverview(),
            'exceptions' => $this->getExceptionOverview(),
            'performance' => $this->getPerformanceOverview(),
            'database' => $this->getDatabaseOverview(),
            'jobs' => $this->getJobOverview(),
            'commands' => $this->getCommandOverview(),
            'scheduler' => $this->getSchedulerOverview(),
            'recent_activity' => $this->getRecentActivity(),
            'recent_slow_requests' => $this->getRecentSlowRequests(),
            'component_health' => $this->getComponentHealth(),
            'has_data' => $this->hasData(),
        ];
    }

    /**
     * Get project header info.
     */
    public function getHeader(): array
    {
        $agentToken = $this->project->agentToken;
        $isConnected = $this->project->is_connected;
        $lastConnectedAt = $this->project->last_connected_at;

        // Calculate last activity time from telemetry
        $lastActivity = $this->getLastActivityTime();

        // Determine connection status
        $connectionStatus = 'Disconnected';
        $connectionStatusColor = 'gray';

        if ($agentToken && $agentToken->isActive() && $isConnected) {
            $connectionStatus = 'Agent Connected';
            $connectionStatusColor = 'green';

            // Check if agent is still active (connected within last 5 minutes)
            if ($lastConnectedAt && $lastConnectedAt->lt(now()->subMinutes(5))) {
                $connectionStatus = 'Agent Idle';
                $connectionStatusColor = 'yellow';
            }
        } elseif ($agentToken && $agentToken->isRevoked()) {
            $connectionStatus = 'Token Revoked';
            $connectionStatusColor = 'red';
        }

        return [
            'name' => $this->project->name,
            'status' => $this->project->status === 'active' ? 'Healthy' : 'Inactive',
            'environment' => $this->project->environment ?? 'Unknown',
            'connection_status' => $connectionStatus,
            'connection_status_color' => $connectionStatusColor,
            'is_connected' => $isConnected,
            'last_activity' => $lastActivity,
            'last_connected_at' => $lastConnectedAt?->toIso8601String(),
            'agent_version' => $agentToken?->last_version ?? null,
            'framework' => $this->project->framework,
        ];
    }

    /**
     * Get the last activity time from telemetry.
     */
    protected function getLastActivityTime(): ?array
    {
        $lastRequest = RequestEvent::where('project_id', $this->project->id)
            ->latest('requested_at')
            ->value('requested_at');

        $lastException = ExceptionOccurrence::where('project_id', $this->project->id)
            ->latest('occurred_at')
            ->value('occurred_at');

        $lastJob = JobEvent::where('project_id', $this->project->id)
            ->latest('created_at')
            ->value('created_at');

        $lastCommand = CommandEvent::where('project_id', $this->project->id)
            ->latest('created_at')
            ->value('created_at');

        // Collect Carbon objects (not timestamps) to find the latest
        $timestamps = array_filter([
            $lastRequest,
            $lastException,
            $lastJob,
            $lastCommand,
        ]);

        if (empty($timestamps)) {
            return null;
        }

        // Get the latest Carbon instance
        $latest = max($timestamps);
        $diff = $latest->diffInSeconds(now());

        $text = $this->formatTimeAgo((int) floor($diff));

        return [
            'timestamp' => $latest->toIso8601String(),
            'text' => $text,
            'seconds_ago' => $diff,
        ];
    }

    /**
     * Format seconds into human-readable time ago.
     */
    protected function formatTimeAgo(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds ago';
        }

        if ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        }

        if ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        }

        $days = floor($seconds / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }

    /**
     * Calculate application health based on telemetry.
     */
    public function getHealth(): array
    {
        // Check if we have any data
        if (!$this->hasData()) {
            return [
                'status' => 'no_data',
                'label' => 'No Data',
                'description' => 'Insufficient telemetry data to determine health.',
                'color' => 'gray',
            ];
        }

        $issues = [];
        $warnings = [];

        // Check request errors (last 1 hour for health)
        $recentFrom = now()->subHour()->toDateTimeString();
        $errorRequests = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $recentFrom)
            ->where('status_code', '>=', 500)
            ->count();

        if ($errorRequests > 10) {
            $issues[] = "$errorRequests server errors in the last hour";
        } elseif ($errorRequests > 0) {
            $warnings[] = "$errorRequests server errors in the last hour";
        }

        // Check exception activity
        $recentExceptions = ExceptionOccurrence::where('project_id', $this->project->id)
            ->where('occurred_at', '>=', $this->from)
            ->count();

        $openExceptions = ExceptionGroup::where('project_id', $this->project->id)
            ->where('status', 'open')
            ->count();

        if ($openExceptions > 5) {
            $issues[] = "$openExceptions open exceptions";
        } elseif ($openExceptions > 0) {
            $warnings[] = "$openExceptions open exceptions";
        }

        // Check recent exceptions (last hour)
        $recentExceptionCount = ExceptionOccurrence::where('project_id', $this->project->id)
            ->where('occurred_at', '>=', $recentFrom)
            ->count();

        if ($recentExceptionCount > 5) {
            $issues[] = "$recentExceptionCount exceptions in the last hour";
        } elseif ($recentExceptionCount > 0) {
            $warnings[] = "$recentExceptionCount exceptions in the last hour";
        }

        // Check performance degradation
        $avgDuration = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->whereNotNull('duration_ms')
            ->avg('duration_ms');

        if ($avgDuration && $avgDuration > 3000) {
            $warnings[] = 'Slow average response time (' . round($avgDuration) . 'ms)';
        }

        // Check slow requests
        $slowThreshold = (int) config('watchtower.slow_request_threshold', 1000);
        $slowRequests = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->where('duration_ms', '>=', $slowThreshold)
            ->count();

        if ($slowRequests > 50) {
            $warnings[] = "$slowRequests slow requests";
        }

        // Check failed jobs
        $failedJobs = JobEvent::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from)
            ->where('status', 'failed')
            ->count();

        if ($failedJobs > 3) {
            $issues[] = "$failedJobs failed jobs";
        } elseif ($failedJobs > 0) {
            $warnings[] = "$failedJobs failed jobs";
        }

        // Check failed commands
        $failedCommands = CommandEvent::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from)
            ->where('status', 'failed')
            ->count();

        if ($failedCommands > 3) {
            $issues[] = "$failedCommands failed commands";
        } elseif ($failedCommands > 0) {
            $warnings[] = "$failedCommands failed commands";
        }

        // Check scheduler failures
        $failedSchedulerTasks = SchedulerTask::where('project_id', $this->project->id)
            ->where('last_status', 'failed')
            ->count();

        $missedSchedulerTasks = SchedulerTask::where('project_id', $this->project->id)
            ->where('last_status', 'missed')
            ->count();

        if ($failedSchedulerTasks > 0 || $missedSchedulerTasks > 0) {
            $warnings[] = "$failedSchedulerTasks failed, $missedSchedulerTasks missed scheduler tasks";
        }

        // Determine health status
        if (!empty($issues)) {
            return [
                'status' => 'critical',
                'label' => 'Critical',
                'description' => 'Multiple failures detected: ' . implode('; ', $issues),
                'color' => 'red',
                'issues' => $issues,
                'warnings' => $warnings,
            ];
        }

        if (!empty($warnings)) {
            return [
                'status' => 'warning',
                'label' => 'Warning',
                'description' => 'Elevated errors and slow requests detected.',
                'color' => 'yellow',
                'issues' => $issues,
                'warnings' => $warnings,
            ];
        }

        return [
            'status' => 'healthy',
            'label' => 'Healthy',
            'description' => 'All monitored components are operating normally.',
            'color' => 'green',
            'issues' => [],
            'warnings' => [],
        ];
    }

    /**
     * Get request overview.
     */
    public function getRequestOverview(): array
    {
        $query = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from);

        $total = (clone $query)->count();

        // Calculate requests per minute
        $minutes = match ($this->timeRange) {
            '1h' => 60,
            '24h' => 1440,
            '7d' => 10080,
            '30d' => 43200,
            default => 1440,
        };

        $requestsPerMinute = $total > 0 ? round($total / max($minutes, 1), 1) : 0;

        // Error rate
        $errors = (clone $query)->where('status_code', '>=', 400)->count();
        $errorRate = $total > 0 ? round(($errors / $total) * 100, 1) : 0;

        // Response times
        $avgDuration = (clone $query)->whereNotNull('duration_ms')->avg('duration_ms');
        $p95Duration = $this->calculatePercentile(
            RequestEvent::where('project_id', $this->project->id)
                ->where('requested_at', '>=', $this->from)
                ->whereNotNull('duration_ms'),
            0.95
        );

        return [
            'total' => $total,
            'requests_per_minute' => $requestsPerMinute,
            'error_rate' => $errorRate,
            'error_count' => $errors,
            'avg_duration_ms' => $avgDuration !== null ? round((float) $avgDuration) : null,
            'p95_duration_ms' => $p95Duration,
        ];
    }

    /**
     * Get exception overview.
     */
    public function getExceptionOverview(): array
    {
        $openGroups = ExceptionGroup::where('project_id', $this->project->id)
            ->where('status', 'open')
            ->count();

        $resolvedGroups = ExceptionGroup::where('project_id', $this->project->id)
            ->where('status', 'resolved')
            ->where('updated_at', '>=', $this->from)
            ->count();

        $newOccurrences = ExceptionOccurrence::where('project_id', $this->project->id)
            ->where('occurred_at', '>=', $this->from)
            ->count();

        // Get recent exceptions with their groups
        $recentExceptions = ExceptionOccurrence::where('project_id', $this->project->id)
            ->where('occurred_at', '>=', $this->from)
            ->with('exceptionGroup')
            ->orderByDesc('occurred_at')
            ->limit(5)
            ->get()
            ->map(function ($occurrence) {
                return [
                    'uuid' => $occurrence->exception_group_uuid,
                    'exception_type' => $occurrence->exception_class_name ?? class_basename($occurrence->exception_class ?? 'Unknown'),
                    'message' => $occurrence->message ? substr($occurrence->message, 0, 100) : 'No message',
                    'occurred_at' => $occurrence->occurred_at?->toIso8601String(),
                    'time_ago' => $occurrence->occurred_at ? $occurrence->occurred_at->diffInMinutes(now()) . ' min ago' : null,
                ];
            })
            ->toArray();

        return [
            'open' => $openGroups,
            'resolved' => $resolvedGroups,
            'new' => $newOccurrences,
            'recent' => $recentExceptions,
        ];
    }

    /**
     * Get performance overview.
     */
    public function getPerformanceOverview(): array
    {
        $query = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->whereNotNull('duration_ms');

        $avgDuration = (clone $query)->avg('duration_ms');
        $p95Duration = $this->calculatePercentile($query, 0.95);
        $p99Duration = $this->calculatePercentile(
            RequestEvent::where('project_id', $this->project->id)
                ->where('requested_at', '>=', $this->from)
                ->whereNotNull('duration_ms'),
            0.99
        );

        $slowThreshold = (int) config('watchtower.slow_request_threshold', 1000);
        $slowRequests = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->where('duration_ms', '>=', $slowThreshold)
            ->count();

        return [
            'avg_duration_ms' => $avgDuration !== null ? round((float) $avgDuration) : null,
            'p95_duration_ms' => $p95Duration,
            'p99_duration_ms' => $p99Duration,
            'slow_requests' => $slowRequests,
            'slow_threshold_ms' => $slowThreshold,
        ];
    }

    /**
     * Get database/SQL overview.
     */
    public function getDatabaseOverview(): array
    {
        $query = QueryEvent::where('project_id', $this->project->id)
            ->where('occurred_at', '>=', $this->from);

        $total = (clone $query)->count();
        $slowQueries = (clone $query)->where('is_slow', true)->count();
        $avgDuration = (clone $query)->whereNotNull('duration_ms')->avg('duration_ms');

        // Get slowest query if any
        $slowestQuery = (clone $query)
            ->where('is_slow', true)
            ->orderByDesc('duration_ms')
            ->first();

        return [
            'total' => $total,
            'slow_queries' => $slowQueries,
            'avg_duration_ms' => $avgDuration !== null ? round((float) $avgDuration, 1) : null,
            'slowest_query' => $slowestQuery ? [
                'sql' => substr($slowestQuery->sql, 0, 100),
                'duration_ms' => $slowestQuery->duration_ms,
            ] : null,
        ];
    }

    /**
     * Get job overview.
     */
    public function getJobOverview(): array
    {
        $query = JobEvent::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from);

        $total = (clone $query)->count();
        $failed = (clone $query)->where('status', 'failed')->count();
        $running = (clone $query)->where('status', 'started')->count();

        // Get recent failed jobs
        $recentFailedJobs = (clone $query)
            ->where('status', 'failed')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function ($job) {
                return [
                    'uuid' => $job->uuid,
                    'job_name' => class_basename($job->job_name ?? 'Unknown'),
                    'exception_message' => $job->exception_message ? substr($job->exception_message, 0, 80) : null,
                    'failed_at' => $job->failed_at ? date('H:i', $job->failed_at) : null,
                ];
            })
            ->toArray();

        return [
            'total' => $total,
            'failed' => $failed,
            'running' => $running,
            'recent_failed' => $recentFailedJobs,
        ];
    }

    /**
     * Get command overview.
     */
    public function getCommandOverview(): array
    {
        $query = CommandEvent::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from);

        $total = (clone $query)->count();
        $failed = (clone $query)->where('status', 'failed')->count();

        $slowThreshold = (int) config('watchtower.command_monitoring.slow_threshold_ms', 1000);
        $slow = (clone $query)->where('duration_ms', '>=', $slowThreshold)->count();

        // Get recent failed commands
        $recentFailedCommands = (clone $query)
            ->where('status', 'failed')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function ($cmd) {
                return [
                    'uuid' => $cmd->uuid,
                    'command_name' => $cmd->command_name,
                    'exception_message' => $cmd->exception_message ? substr($cmd->exception_message, 0, 80) : null,
                    'failed_at' => $cmd->finished_at ? date('H:i', $cmd->finished_at) : null,
                ];
            })
            ->toArray();

        return [
            'total' => $total,
            'failed' => $failed,
            'slow' => $slow,
            'recent_failed' => $recentFailedCommands,
        ];
    }

    /**
     * Get scheduler overview.
     */
    public function getSchedulerOverview(): array
    {
        $tasks = SchedulerTask::where('project_id', $this->project->id)->get();

        $total = $tasks->count();
        $healthy = $tasks->where('last_status', 'completed')->count() + $tasks->where('last_status', 'healthy')->count();
        $failed = $tasks->where('last_status', 'failed')->count();
        $missed = $tasks->where('last_status', 'missed')->count();

        // Get recent failed/missed executions
        $recentIssues = SchedulerExecution::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from)
            ->whereIn('status', ['failed', 'missed'])
            ->with('schedulerTask')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function ($execution) {
                return [
                    'uuid' => $execution->uuid,
                    'task_name' => $execution->schedulerTask?->task_name ?? $execution->command_name ?? 'Unknown',
                    'status' => $execution->status,
                    'exception_message' => $execution->exception_message ? substr($execution->exception_message, 0, 80) : null,
                    'created_at' => $execution->created_at->format('H:i'),
                ];
            })
            ->toArray();

        return [
            'total' => $total,
            'healthy' => $healthy,
            'failed' => $failed,
            'missed' => $missed,
            'recent_issues' => $recentIssues,
        ];
    }

    /**
     * Get recent activity from all sources.
     */
    public function getRecentActivity(): array
    {
        $activities = [];

        // Request errors
        $errorRequests = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->where('status_code', '>=', 400)
            ->orderByDesc('requested_at')
            ->limit(5)
            ->get();

        foreach ($errorRequests as $request) {
            $activities[] = [
                'type' => 'request_error',
                'title' => 'Request error',
                'subtitle' => $request->method . ' ' . $request->path . ' - ' . $request->status_code,
                'uuid' => $request->uuid,
                'time' => $request->requested_at->format('H:i'),
                'timestamp' => $request->requested_at->timestamp,
            ];
        }

        // Exceptions
        $exceptions = ExceptionOccurrence::where('project_id', $this->project->id)
            ->where('occurred_at', '>=', $this->from)
            ->orderByDesc('occurred_at')
            ->limit(5)
            ->get();

        foreach ($exceptions as $exception) {
            $activities[] = [
                'type' => 'exception',
                'title' => 'Exception occurred',
                'subtitle' => $exception->exception_class_name ?? class_basename($exception->exception_class ?? 'Unknown'),
                'uuid' => $exception->exception_group_uuid,
                'time' => $exception->occurred_at->format('H:i'),
                'timestamp' => $exception->occurred_at->timestamp,
            ];
        }

        // Failed jobs
        $failedJobs = JobEvent::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from)
            ->where('status', 'failed')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        foreach ($failedJobs as $job) {
            $activities[] = [
                'type' => 'job_failed',
                'title' => 'Job failed',
                'subtitle' => class_basename($job->job_name ?? 'Unknown'),
                'uuid' => $job->uuid,
                'time' => $job->created_at->format('H:i'),
                'timestamp' => $job->created_at->timestamp,
            ];
        }

        // Failed commands
        $failedCommands = CommandEvent::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from)
            ->where('status', 'failed')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        foreach ($failedCommands as $cmd) {
            $activities[] = [
                'type' => 'command_failed',
                'title' => 'Command failed',
                'subtitle' => $cmd->command_name,
                'uuid' => $cmd->uuid,
                'time' => $cmd->created_at->format('H:i'),
                'timestamp' => $cmd->created_at->timestamp,
            ];
        }

        // Scheduler failures/misses
        $schedulerIssues = SchedulerExecution::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from)
            ->whereIn('status', ['failed', 'missed'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        foreach ($schedulerIssues as $execution) {
            $activities[] = [
                'type' => 'scheduler_' . $execution->status,
                'title' => $execution->status === 'missed' ? 'Scheduler missed' : 'Scheduler failed',
                'subtitle' => $execution->command_name ?? $execution->schedulerTask?->task_name ?? 'Unknown',
                'uuid' => $execution->uuid,
                'time' => $execution->created_at->format('H:i'),
                'timestamp' => $execution->created_at->timestamp,
            ];
        }

        // Sort by timestamp descending and take the most recent 10
        usort($activities, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return array_slice($activities, 0, 10);
    }

    /**
     * Get recent slow requests.
     */
    public function getRecentSlowRequests(): array
    {
        $slowThreshold = (int) config('watchtower.slow_request_threshold', 1000);

        return RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->where('duration_ms', '>=', $slowThreshold)
            ->orderByDesc('duration_ms')
            ->limit(5)
            ->get()
            ->map(function ($request) {
                return [
                    'uuid' => $request->uuid,
                    'method' => $request->method,
                    'path' => $request->path,
                    'duration_ms' => (int) $request->duration_ms,
                    'time' => $request->requested_at->format('H:i'),
                ];
            })
            ->toArray();
    }

    /**
     * Get component health summary.
     */
    public function getComponentHealth(): array
    {
        $components = [];

        // Requests
        $errorRequests = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->where('status_code', '>=', 500)
            ->count();

        $components['requests'] = [
            'status' => $errorRequests > 5 ? 'warning' : 'healthy',
            'label' => 'Requests',
        ];

        // Exceptions
        $openExceptions = ExceptionGroup::where('project_id', $this->project->id)
            ->where('status', 'open')
            ->count();

        $components['exceptions'] = [
            'status' => $openExceptions > 5 ? 'critical' : ($openExceptions > 0 ? 'warning' : 'healthy'),
            'label' => 'Exceptions',
        ];

        // Database
        $slowQueries = QueryEvent::where('project_id', $this->project->id)
            ->where('occurred_at', '>=', $this->from)
            ->where('is_slow', true)
            ->count();

        $components['database'] = [
            'status' => $slowQueries > 20 ? 'warning' : 'healthy',
            'label' => 'Database',
        ];

        // Jobs
        $failedJobs = JobEvent::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from)
            ->where('status', 'failed')
            ->count();

        $components['jobs'] = [
            'status' => $failedJobs > 3 ? 'warning' : 'healthy',
            'label' => 'Jobs',
        ];

        // Commands
        $failedCommands = CommandEvent::where('project_id', $this->project->id)
            ->where('created_at', '>=', $this->from)
            ->where('status', 'failed')
            ->count();

        $components['commands'] = [
            'status' => $failedCommands > 3 ? 'warning' : 'healthy',
            'label' => 'Commands',
        ];

        // Scheduler
        $schedulerIssues = SchedulerTask::where('project_id', $this->project->id)
            ->whereIn('last_status', ['failed', 'missed'])
            ->count();

        $components['scheduler'] = [
            'status' => $schedulerIssues > 0 ? 'warning' : 'healthy',
            'label' => 'Scheduler',
        ];

        // Performance (based on slow requests percentage)
        $totalRequests = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->count();

        $slowThreshold = (int) config('watchtower.slow_request_threshold', 1000);
        $slowRequests = RequestEvent::where('project_id', $this->project->id)
            ->where('requested_at', '>=', $this->from)
            ->where('duration_ms', '>=', $slowThreshold)
            ->count();

        $slowRate = $totalRequests > 0 ? ($slowRequests / $totalRequests) * 100 : 0;
        $components['performance'] = [
            'status' => $slowRate > 10 ? 'warning' : 'healthy',
            'label' => 'Performance',
        ];

        return $components;
    }

    /**
     * Check if project has any telemetry data.
     */
    public function hasData(): bool
    {
        return RequestEvent::where('project_id', $this->project->id)->exists()
            || ExceptionOccurrence::where('project_id', $this->project->id)->exists()
            || JobEvent::where('project_id', $this->project->id)->exists()
            || CommandEvent::where('project_id', $this->project->id)->exists()
            || QueryEvent::where('project_id', $this->project->id)->exists();
    }

    /**
     * Calculate percentile using subquery (MySQL compatible).
     */
    protected function calculatePercentile($query, float $percentile): ?int
    {
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
}
