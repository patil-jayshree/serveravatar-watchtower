<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RequestEventController extends Controller
{
    public function index(Request $request, Organization $organization, Project $project): Response
    {
        // Ensure user owns the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $query = $project->requestEvents()
            ->orderByDesc('requested_at');

        // Filters
        if ($request->filled('method')) {
            $query->where('method', strtoupper($request->input('method')));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'success') {
                $query->whereBetween('status_code', [200, 299]);
            } elseif ($status === 'error') {
                $query->where('status_code', '>=', 400);
            } elseif ($status === 'redirect') {
                $query->whereBetween('status_code', [300, 399]);
            } else {
                $query->where('status_code', (int) $status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('path', 'like', "%{$search}%");
        }

        if ($request->filled('environment')) {
            $query->where('environment', $request->input('environment'));
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $events = $query->paginate($perPage);

        // Summary stats
        $stats = [
            'total' => $project->requestEvents()->count(),
            'successful' => $project->requestEvents()->whereBetween('status_code', [200, 299])->count(),
            'errors' => $project->requestEvents()->where('status_code', '>=', 400)->count(),
            'avg_duration' => round($project->requestEvents()->avg('duration_ms') ?? 0),
        ];

        return response()->view('projects.requests.index', [
            'organization' => $project->organization,
            'project' => $project,
            'events' => $events,
            'stats' => $stats,
            'filters' => $request->only(['method', 'status', 'search', 'environment', 'per_page']),
        ]);
    }

    public function show(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $event = $project->requestEvents()->where('uuid', $uuid)->firstOrFail();

        return response()->view('projects.requests.show', [
            'organization' => $project->organization,
            'project' => $project,
            'event' => $event,
        ]);
    }
}
