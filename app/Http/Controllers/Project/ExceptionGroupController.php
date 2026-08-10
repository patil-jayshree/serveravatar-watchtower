<?php

declare(strict_types=1);

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\ExceptionGroup;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ExceptionGroupController extends Controller
{
    public function index(Request $request, Organization $organization, Project $project): Response
    {
        // Ensure user owns the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $query = $project->exceptionGroups()
            ->with('latestOccurrence')
            ->orderByDesc('last_seen_at');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('exception_type', 'like', '%' . $request->input('type') . '%');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('exception_type', 'like', "%{$search}%")
                    ->orWhere('normalized_message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('environment')) {
            $query->whereHas('occurrences', function ($q) use ($request) {
                $q->where('environment', $request->input('environment'));
            });
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $groups = $query->paginate($perPage);

        // Summary stats
        $stats = [
            'total' => $project->exceptionGroups()->count(),
            'open' => $project->exceptionGroups()->where('status', 'open')->count(),
            'resolved' => $project->exceptionGroups()->where('status', 'resolved')->count(),
        ];

        return response()->view('projects.exceptions.index', [
            'organization' => $project->organization,
            'project' => $project,
            'groups' => $groups,
            'stats' => $stats,
            'filters' => $request->only(['status', 'type', 'search', 'environment', 'per_page']),
        ]);
    }

    public function show(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $group = $project->exceptionGroups()->where('uuid', $uuid)->firstOrFail();

        $query = $group->occurrences()
            ->orderByDesc('occurred_at');

        $perPage = min((int) $request->input('per_page', 25), 100);
        $occurrences = $query->paginate($perPage);

        return response()->view('projects.exceptions.show', [
            'organization' => $project->organization,
            'project' => $project,
            'group' => $group,
            'occurrences' => $occurrences,
        ]);
    }

    public function updateStatus(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $group = $project->exceptionGroups()->where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', 'in:open,resolved'],
        ]);

        if ($validated['status'] === 'resolved') {
            $group->markAsResolved();
        } else {
            $group->markAsOpen();
        }

        return response()->json([
            'success' => true,
            'status' => $group->status,
        ]);
    }
}
