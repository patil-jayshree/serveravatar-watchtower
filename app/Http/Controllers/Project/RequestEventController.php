<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

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

        return Inertia::render('Projects/Requests/Index', [
            'organization' => [
                'id' => $project->organization->id,
                'name' => $project->organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'requests' => $events->items(),
            'stats' => $stats,
            'filters' => $request->only(['method', 'status', 'search', 'environment', 'per_page']),
        ]);
    }

    public function show(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        // Support both uuid and request_id lookup
        $event = $project->requestEvents()
            ->where(function ($query) use ($uuid) {
                $query->where('uuid', $uuid)
                    ->orWhere('request_id', $uuid);
            })
            ->firstOrFail();

        return Inertia::render('Projects/Requests/Show', [
            'organization' => [
                'id' => $project->organization->id,
                'name' => $project->organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'event' => $event,
        ]);
    }
}
