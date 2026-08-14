<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;
use App\Models\Project;
use App\Models\RequestEvent;
use App\Models\ExceptionGroup;
use App\Models\ExceptionOccurrence;
use App\Models\JobEvent;
use App\Models\CommandEvent;
use App\Models\SchedulerExecution;
use App\Models\SchedulerTask;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAggregationService
{
    protected Organization $organization;
    protected string $timeRange;
    protected string $from;

    public function __construct(Organization $organization, string $timeRange = '24h')
    {
        $this->organization = $organization;
        $this->timeRange = $timeRange;
        $this->from = $this->resolveTimeRange($timeRange);
    }

    /**
     * Resolve time range string to ISO timestamp.
     */
    protected function resolveTimeRange(string $timeRange): string
    {
        $from = match ($timeRange) {
            '1h' => Carbon::now()->subHour(),
            '24h' => Carbon::now()->subHours(24),
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            default => Carbon::now()->subHours(24),
        };

        return $from->toDateTimeString();
    }

    /**
     * Get the from date for queries.
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * Get time range label.
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
     * Get all dashboard data at once.
     */
    public function getDashboardData(): array
    {
        return [
            'header' => $this->getHeader(),
            'projects_summary' => $this->getProjectsSummary(),
            'requests' => $this->getRequestsSummary(),
            'exceptions' => $this->getExceptionsSummary(),
            'jobs' => $this->getJobsSummary(),
            'commands' => $this->getCommandsSummary(),
            'scheduler' => $this->getSchedulerSummary(),
            'health' => $this->getHealthSummary(),
            'recent_activity' => $this->getRecentActivity(),
            'projects_needing_attention' => $this->getProjectsNeedingAttention(),
        ];
    }

    /**
     * Get organization header data.
     */
    protected function getHeader(): array
    {
        return [
            'name' => $this->organization->name,
            'project_count' => $this->organization->projects()->count(),
            'connected_projects' => $this->organization->projects()->where('is_connected', true)->count(),
        ];
    }

    /**
     * Get projects summary by status.
     */
    protected function getProjectsSummary(): array
    {
        $projects = $this->organization->projects;
        $total = $projects->count();
        $connected = $projects->where('is_connected', true)->count();
        $disconnected = $total - $connected;

        // Calculate health for connected projects
        $healthCounts = [
            'healthy' => 0,
            'warning' => 0,
            'critical' => 0,
            'no_data' => $disconnected,
        ];

        if ($connected > 0) {
            foreach ($projects->where('is_connected', true) as $project) {
                $health = $this->calculateProjectHealth($project);
                $healthCounts[$health]++;
            }
        }

        return [
            'total' => $total,
            'connected' => $connected,
            'disconnected' => $disconnected,
            'healthy' => $healthCounts['healthy'],
            'warning' => $healthCounts['warning'],
            'critical' => $healthCounts['critical'],
            'no_data' => $healthCounts['no_data'],
        ];
    }

    /**
     * Calculate health for a single project.
     */
    protected function calculateProjectHealth(Project $project): string
    {
        $from = $this->from;

        // Check for errors in last period
        $errorCount = $project->exceptionGroups()
            ->whereIn('status', ['open', 'new'])
            ->where('first_seen_at', '>=', $from)
            ->count();

        $failedJobs = $project->jobEvents()
            ->where('status', 'failed')
            ->where('created_at', '>=', $from)
            ->count();

        $failedCommands = $project->commandEvents()
            ->where('exit_code', '!=', 0)
            ->where('created_at', '>=', $from)
            ->count();

        // Check request error rate
        $requestStats = $project->requestEvents()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status_code >= 500 THEN 1 ELSE 0 END) as errors')
            ->where('created_at', '>=', $from)
            ->first();

        $totalRequests = $requestStats->total ?? 0;
        $errorRequests = $requestStats->errors ?? 0;
        $errorRate = $totalRequests > 0 ? ($errorRequests / $totalRequests) * 100 : 0;

        // Determine health
        if ($errorCount > 10 || $errorRate > 5 || $failedJobs > 5 || $failedCommands > 5) {
            return 'critical';
        }

        if ($errorCount > 0 || $errorRate > 1 || $failedJobs > 0 || $failedCommands > 0) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Get requests summary across all org projects.
     */
    protected function getRequestsSummary(): array
    {
        $projectIds = $this->organization->projects()->where('is_connected', true)->pluck('id');

        if ($projectIds->isEmpty()) {
            return [
                'total' => 0,
                'errors' => 0,
                'error_rate' => 0,
                'avg_duration_ms' => null,
                'p95_duration_ms' => null,
            ];
        }

        $stats = RequestEvent::whereIn('project_id', $projectIds)
            ->where('created_at', '>=', $this->from)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status_code >= 500 THEN 1 ELSE 0 END) as errors,
                AVG(duration_ms) as avg_duration
            ')
            ->first();

        $total = (int) ($stats->total ?? 0);
        $errors = (int) ($stats->errors ?? 0);
        $errorRate = $total > 0 ? round(($errors / $total) * 100, 2) : 0;
        $avgDuration = $stats->avg_duration ? round((float) $stats->avg_duration, 2) : null;

        // Calculate P95 in PHP for MariaDB compatibility
        $p95Duration = null;
        if ($total > 0) {
            $durations = RequestEvent::whereIn('project_id', $projectIds)
                ->where('created_at', '>=', $this->from)
                ->whereNotNull('duration_ms')
                ->pluck('duration_ms')
                ->sort()
                ->values();

            if ($durations->count() > 0) {
                $p95Index = (int) floor($durations->count() * 0.95);
                $p95Duration = round((float) $durations->get($p95Index), 2);
            }
        }

        return [
            'total' => $total,
            'errors' => $errors,
            'error_rate' => $errorRate,
            'avg_duration_ms' => $avgDuration,
            'p95_duration_ms' => $p95Duration,
        ];
    }

    /**
     * Get exceptions summary.
     */
    protected function getExceptionsSummary(): array
    {
        $projectIds = $this->organization->projects()->where('is_connected', true)->pluck('id');

        if ($projectIds->isEmpty()) {
            return [
                'open' => 0,
                'new' => 0,
                'resolved' => 0,
            ];
        }

        $open = ExceptionGroup::whereIn('project_id', $projectIds)
            ->whereIn('status', ['open', 'new'])
            ->where('updated_at', '>=', $this->from)
            ->count();

        $new = ExceptionGroup::whereIn('project_id', $projectIds)
            ->where('status', 'new')
            ->where('first_seen_at', '>=', $this->from)
            ->count();

        $resolved = ExceptionGroup::whereIn('project_id', $projectIds)
            ->where('status', 'resolved')
            ->where('updated_at', '>=', $this->from)
            ->count();

        return [
            'open' => $open,
            'new' => $new,
            'resolved' => $resolved,
        ];
    }

    /**
     * Get jobs summary.
     */
    protected function getJobsSummary(): array
    {
        $projectIds = $this->organization->projects()->where('is_connected', true)->pluck('id');

        if ($projectIds->isEmpty()) {
            return [
                'total' => 0,
                'failed' => 0,
                'pending' => 0,
                'running' => 0,
            ];
        }

        $stats = JobEvent::whereIn('project_id', $projectIds)
            ->where('created_at', '>=', $this->from)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "running" THEN 1 ELSE 0 END) as running
            ')
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'failed' => (int) ($stats->failed ?? 0),
            'pending' => (int) ($stats->pending ?? 0),
            'running' => (int) ($stats->running ?? 0),
        ];
    }

    /**
     * Get commands summary.
     */
    protected function getCommandsSummary(): array
    {
        $projectIds = $this->organization->projects()->where('is_connected', true)->pluck('id');

        if ($projectIds->isEmpty()) {
            return [
                'total' => 0,
                'failed' => 0,
            ];
        }

        $stats = CommandEvent::whereIn('project_id', $projectIds)
            ->where('created_at', '>=', $this->from)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN exit_code != 0 THEN 1 ELSE 0 END) as failed
            ')
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'failed' => (int) ($stats->failed ?? 0),
        ];
    }

    /**
     * Get scheduler summary.
     */
    protected function getSchedulerSummary(): array
    {
        $projectIds = $this->organization->projects()->where('is_connected', true)->pluck('id');

        if ($projectIds->isEmpty()) {
            return [
                'total' => 0,
                'healthy' => 0,
                'failed' => 0,
                'missed' => 0,
            ];
        }

        $stats = SchedulerExecution::whereIn('project_id', $projectIds)
            ->where('created_at', '>=', $this->from)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as healthy,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = "missed" THEN 1 ELSE 0 END) as missed
            ')
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'healthy' => (int) ($stats->healthy ?? 0),
            'failed' => (int) ($stats->failed ?? 0),
            'missed' => (int) ($stats->missed ?? 0),
        ];
    }

    /**
     * Get overall health summary.
     */
    protected function getHealthSummary(): array
    {
        $summary = $this->getProjectsSummary();
        $requests = $this->getRequestsSummary();
        $exceptions = $this->getExceptionsSummary();
        $jobs = $this->getJobsSummary();
        $commands = $this->getCommandsSummary();
        $scheduler = $this->getSchedulerSummary();

        $warnings = [];

        if ($exceptions['open'] > 0) {
            $warnings[] = "{$exceptions['open']} open exception groups";
        }

        if ($jobs['failed'] > 0) {
            $warnings[] = "{$jobs['failed']} failed jobs";
        }

        if ($commands['failed'] > 0) {
            $warnings[] = "{$commands['failed']} failed commands";
        }

        if ($scheduler['failed'] > 0) {
            $warnings[] = "{$scheduler['failed']} failed scheduler tasks";
        }

        if ($scheduler['missed'] > 0) {
            $warnings[] = "{$scheduler['missed']} missed scheduler tasks";
        }

        if ($requests['error_rate'] > 5) {
            $warnings[] = "High error rate: {$requests['error_rate']}%";
        }

        // Determine overall status
        if ($summary['total'] === 0 || $summary['connected'] === 0) {
            return [
                'status' => 'no_data',
                'label' => 'No Data',
                'description' => 'No connected projects to monitor.',
                'color' => 'no-data',
                'warnings' => [],
            ];
        }

        if ($summary['critical'] > 0 || $requests['error_rate'] > 5 || count($warnings) > 3) {
            return [
                'status' => 'critical',
                'label' => 'Critical',
                'description' => 'Multiple issues detected across projects.',
                'color' => 'critical',
                'warnings' => $warnings,
            ];
        }

        if ($summary['warning'] > 0 || count($warnings) > 0 || $requests['error_rate'] > 1) {
            return [
                'status' => 'warning',
                'label' => 'Warning',
                'description' => 'Some issues need attention.',
                'color' => 'warning',
                'warnings' => $warnings,
            ];
        }

        return [
            'status' => 'healthy',
            'label' => 'Healthy',
            'description' => 'All systems operating normally.',
            'color' => 'healthy',
            'warnings' => [],
        ];
    }

    /**
     * Get recent activity across all projects.
     */
    protected function getRecentActivity(): array
    {
        $activities = [];
        $projectIds = $this->organization->projects()->where('is_connected', true)->pluck('id');

        if ($projectIds->isEmpty()) {
            return [];
        }

        // Get recent exceptions
        $recentExceptions = ExceptionGroup::whereIn('project_id', $projectIds)
            ->where('updated_at', '>=', $this->from)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentExceptions as $exception) {
            $activities[] = [
                'type' => 'exception',
                'title' => $exception->exception_type,
                'subtitle' => $exception->message,
                'time' => $exception->updated_at->diffForHumans(),
                'uuid' => $exception->uuid,
                'project_id' => $exception->project_id,
                'severity' => 'error',
            ];
        }

        // Get recent failed jobs
        $recentFailedJobs = JobEvent::whereIn('project_id', $projectIds)
            ->where('status', 'failed')
            ->where('created_at', '>=', $this->from)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentFailedJobs as $job) {
            $activities[] = [
                'type' => 'job_failed',
                'title' => 'Failed Job',
                'subtitle' => $job->job_name,
                'time' => $job->created_at->diffForHumans(),
                'uuid' => $job->uuid,
                'project_id' => $job->project_id,
                'severity' => 'error',
            ];
        }

        // Sort by time
        usort($activities, fn($a, $b) => 0);

        return array_slice($activities, 0, 10);
    }

    /**
     * Get projects that need attention.
     */
    protected function getProjectsNeedingAttention(): array
    {
        $projects = $this->organization->projects->where('is_connected', true);
        $needsAttention = [];

        foreach ($projects as $project) {
            $health = $this->calculateProjectHealth($project);

            if ($health !== 'healthy') {
                $needsAttention[] = [
                    'id' => $project->id,
                    'uuid' => $project->uuid,
                    'name' => $project->name,
                    'health' => $health,
                ];
            }
        }

        return $needsAttention;
    }
}
