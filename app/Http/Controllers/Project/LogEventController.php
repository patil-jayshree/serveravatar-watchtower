<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\ExceptionGroup;
use App\Models\LogEvent;
use App\Models\RequestEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogEventController extends Controller
{
    /**
     * Display a listing of log events for the project.
     */
    public function index(Request $request, string $organization, string $project): Response
    {
        $project = $this->getProject($organization, $project);
        if (!$project) {
            abort(404);
        }

        // Build query
        $query = LogEvent::where('project_id', $project->id);

        // Apply filters
        $filters = $request->only(['search', 'level', 'channel', 'environment', 'time_range']);

        // Level filter
        if (!empty($filters['level']) && $filters['level'] !== 'all') {
            $level = strtoupper($filters['level']);
            if (in_array($level, LogEvent::LEVELS, true)) {
                $query->where('level', $level);
            }
        }

        // Channel filter
        if (!empty($filters['channel']) && $filters['channel'] !== 'all') {
            $query->where('channel', $filters['channel']);
        }

        // Environment filter
        if (!empty($filters['environment']) && $filters['environment'] !== 'all') {
            $query->where('environment', $filters['environment']);
        }

        // Time range filter
        $timeRange = $filters['time_range'] ?? 'all';
        $query->inTimeRange($timeRange);

        // Search
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Order by most recent
        $query->orderByDesc('logged_at');

        // Paginate
        $perPage = min((int) ($request->query('per_page', 25)), 100);
        $logs = $query->paginate($perPage);

        // Get stats for summary
        $stats = [
            'total' => LogEvent::where('project_id', $project->id)->count(),
            'errors' => LogEvent::where('project_id', $project->id)->errors()->count(),
            'warnings' => LogEvent::where('project_id', $project->id)->warnings()->count(),
            'last_24h' => LogEvent::where('project_id', $project->id)
                ->inTimeRange('24h')
                ->count(),
            'errors_24h' => LogEvent::where('project_id', $project->id)
                ->inTimeRange('24h')
                ->errors()
                ->count(),
        ];

        // Get available channels for filter dropdown
        $channels = LogEvent::where('project_id', $project->id)
            ->whereNotNull('channel')
            ->distinct()
            ->pluck('channel')
            ->sort()
            ->values();

        return response()->view('projects.logs.index', [
            'organization' => $project->organization,
            'project' => $project,
            'logs' => $logs,
            'stats' => $stats,
            'channels' => $channels,
            'filters' => $filters,
        ]);
    }

    /**
     * Display a specific log event.
     */
    public function show(Request $request, string $organization, string $project, string $uuid): Response
    {
        $project = $this->getProject($organization, $project);
        if (!$project) {
            abort(404);
        }

        $log = LogEvent::where('uuid', $uuid)
            ->where('project_id', $project->id)
            ->firstOrFail();

        // Get related request if available
        $relatedRequest = null;
        if ($log->request_id) {
            $relatedRequest = RequestEvent::where('request_id', $log->request_id)
                ->where('project_id', $project->id)
                ->first();
        }

        // Get related exception group if available
        $relatedExceptionGroup = $log->getRelatedExceptionGroup();

        // Get other logs from the same request
        $relatedLogs = [];
        if ($log->request_id) {
            $relatedLogs = LogEvent::where('project_id', $project->id)
                ->where('request_id', $log->request_id)
                ->where('uuid', '!=', $log->uuid)
                ->orderByDesc('logged_at')
                ->limit(10)
                ->get();
        }

        return response()->view('projects.logs.show', [
            'organization' => $project->organization,
            'project' => $project,
            'log' => $log,
            'relatedRequest' => $relatedRequest,
            'relatedExceptionGroup' => $relatedExceptionGroup,
            'relatedLogs' => $relatedLogs,
        ]);
    }

    /**
     * Get project by organization UUID (or id) and project UUID.
     */
    protected function getProject(string $organization, string $project): ?\App\Models\Project
    {
        // Organization can be looked up by uuid or id
        $org = \App\Models\Organization::where('uuid', $organization)
            ->orWhere('id', (int) $organization)
            ->first();

        if (!$org) {
            return null;
        }

        return \App\Models\Project::where('organization_id', $org->id)
            ->where(function ($q) use ($project) {
                $q->where('uuid', $project)
                    ->orWhere('id', (int) $project);
            })
            ->first();
    }
}
