<?php

declare(strict_types=1);

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\JobEvent;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class JobEventController extends Controller
{
    public function index(Request $request, Organization $organization, Project $project): Response
    {
        // Ensure user owns the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $query = $project->jobEvents()
            ->orderByDesc('created_at');

        // Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->withStatus($request->input('status'));
        }

        if ($request->filled('queue') && $request->input('queue') !== 'all') {
            $query->onQueue($request->input('queue'));
        }

        if ($request->filled('connection') && $request->input('connection') !== 'all') {
            $query->onConnection($request->input('connection'));
        }

        if ($request->filled('time_range') && $request->input('time_range') !== 'all') {
            $query->inTimeRange($request->input('time_range'));
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $jobs = $query->paginate($perPage);

        // Get distinct queue names for filter
        $queues = $project->jobEvents()
            ->whereNotNull('queue')
            ->distinct()
            ->pluck('queue')
            ->sort()
            ->values();

        // Get distinct connections for filter
        $connections = $project->jobEvents()
            ->whereNotNull('connection')
            ->distinct()
            ->pluck('connection')
            ->sort()
            ->values();

        // Stats
        $stats = [
            'total' => $project->jobEvents()->count(),
            'completed' => $project->jobEvents()->where('status', JobEvent::STATUS_COMPLETED)->count(),
            'failed' => $project->jobEvents()->where('status', JobEvent::STATUS_FAILED)->count(),
            'running' => $project->jobEvents()->where('status', JobEvent::STATUS_STARTED)->count(),
            'avg_duration' => round($project->jobEvents()->whereNotNull('duration_ms')->avg('duration_ms') ?? 0),
        ];

        // Failed jobs grouped by job name
        $failedJobsByName = JobEvent::where('project_id', $project->id)
            ->where('status', JobEvent::STATUS_FAILED)
            ->selectRaw('job_name, COUNT(*) as count')
            ->groupBy('job_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return Inertia::render('Projects/Jobs/Index', [
            'organization' => [
                'id' => $project->organization->id,
                'name' => $project->organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'jobs' => $jobs->items(),
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'queue', 'connection', 'time_range', 'per_page']),
        ]);
    }

    public function show(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $job = JobEvent::where('uuid', $uuid)
            ->where('project_id', $project->id)
            ->firstOrFail();

        // Get related request event if available
        $relatedRequest = null;
        if ($job->request_id) {
            $relatedRequest = \App\Models\RequestEvent::where('request_id', $job->request_id)
                ->where('project_id', $project->id)
                ->first();
        }

        // Get related exception group for failed jobs
        $relatedExceptionGroup = $job->getRelatedExceptionGroup();

        return Inertia::render('Projects/Jobs/Show', [
            'organization' => [
                'id' => $project->organization->id,
                'name' => $project->organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'job' => $job,
            'relatedRequest' => $relatedRequest,
            'relatedExceptionGroup' => $relatedExceptionGroup,
        ]);
    }
}
