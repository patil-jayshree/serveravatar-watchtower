<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Project;
use App\Models\QueryEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class QueryEventController extends Controller
{
    public function index(Request $request, Organization $organization, Project $project): Response
    {
        // Ensure user owns the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $query = $project->queryEvents()
            ->orderByDesc('occurred_at');

        // Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('normalized_sql', 'like', "%{$search}%");
        }

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->where('query_type', strtolower($request->input('type')));
        }

        if ($request->filled('connection') && $request->input('connection') !== 'all') {
            $query->where('connection_name', $request->input('connection'));
        }

        if ($request->filled('slow') && $request->input('slow') !== 'all') {
            if ($request->input('slow') === 'slow') {
                $query->where('is_slow', true);
            } else {
                $query->where('is_slow', false);
            }
        }

        if ($request->filled('time_range') && $request->input('time_range') !== 'all') {
            $range = $request->input('time_range');
            $query->inTimeRange($range);
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $queries = $query->paginate($perPage);

        // Get distinct connection names for filter
        $connections = $project->queryEvents()
            ->whereNotNull('connection_name')
            ->distinct()
            ->pluck('connection_name')
            ->sort()
            ->values();

        // Stats
        $stats = [
            'total' => $project->queryEvents()->count(),
            'slow' => $project->queryEvents()->where('is_slow', true)->count(),
            'avg_duration' => round($project->queryEvents()->avg('duration_ms') ?? 0),
        ];

        return response()->view('projects.queries.index', [
            'organization' => $project->organization,
            'project' => $project,
            'queries' => $queries,
            'connections' => $connections,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'connection', 'slow', 'time_range', 'per_page']),
        ]);
    }

    public function show(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $queryEvent = QueryEvent::where('uuid', $uuid)
            ->where('project_id', $project->id)
            ->firstOrFail();

        // Get related request event if available
        $relatedRequest = null;
        if ($queryEvent->request_id) {
            $relatedRequest = \App\Models\RequestEvent::where('request_id', $queryEvent->request_id)
                ->where('project_id', $project->id)
                ->first();
        }

        return response()->view('projects.queries.show', [
            'organization' => $project->organization,
            'project' => $project,
            'query' => $queryEvent,
            'relatedRequest' => $relatedRequest,
        ]);
    }
}
