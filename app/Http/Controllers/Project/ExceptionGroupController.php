<?php

declare(strict_types=1);

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\ExceptionGroup;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

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

        // Time range filter
        if ($request->filled('time_range')) {
            $timeRange = $request->input('time_range');
            $startDate = match ($timeRange) {
                '24h' => now()->subDay(),
                '7d' => now()->subWeek(),
                '30d' => now()->subMonth(),
                'custom' => $request->filled('start_date')
                    ? \Carbon\Carbon::parse($request->input('start_date'))->startOfDay()
                    : null,
                default => null,
            };

            if ($startDate) {
                $query->where('last_seen_at', '>=', $startDate);
            }
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $groups = $query->paginate($perPage);

        // Summary stats - respect same filters as the main query
        $statsQuery = $project->exceptionGroups();

        if ($request->filled('status')) {
            $statsQuery->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $statsQuery->where('exception_type', 'like', '%' . $request->input('type') . '%');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $statsQuery->where(function ($q) use ($search) {
                $q->where('exception_type', 'like', "%{$search}%")
                    ->orWhere('normalized_message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('environment')) {
            $statsQuery->whereHas('occurrences', function ($q) use ($request) {
                $q->where('environment', $request->input('environment'));
            });
        }

        if ($request->filled('time_range')) {
            $timeRange = $request->input('time_range');
            $startDate = match ($timeRange) {
                '24h' => now()->subDay(),
                '7d' => now()->subWeek(),
                '30d' => now()->subMonth(),
                'custom' => $request->filled('start_date')
                    ? \Carbon\Carbon::parse($request->input('start_date'))->startOfDay()
                    : null,
                default => null,
            };

            if ($startDate) {
                $statsQuery->where('last_seen_at', '>=', $startDate);
            }
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'open' => (clone $statsQuery)->where('status', 'open')->count(),
            'resolved' => (clone $statsQuery)->where('status', 'resolved')->count(),
        ];

        return Inertia::render('Projects/Exceptions/Index', [
            'organization' => [
                'id' => $project->organization->id,
                'name' => $project->organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'exceptions' => $groups->items(),
            'stats' => $stats,
            'filters' => $request->only(['status', 'type', 'search', 'environment', 'per_page', 'time_range', 'start_date', 'end_date']),
        ]);
    }

    public function show(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $group = $project->exceptionGroups()
            ->with(['latestOccurrence.schedulerExecution.schedulerTask'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $query = $group->occurrences()
            ->orderByDesc('occurred_at');

        $perPage = min((int) $request->input('per_page', 25), 100);
        $occurrences = $query->paginate($perPage);

        return Inertia::render('Projects/Exceptions/Show', [
            'organization' => [
                'id' => $project->organization->id,
                'name' => $project->organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'exception' => [
                'id' => $group->id,
                'uuid' => $group->uuid,
                'exception_class' => class_basename($group->exception_type),
                'message' => $group->normalized_message,
                'status' => $group->status,
                'occurrence_count' => $group->occurrence_count,
                'first_occurrence_at' => $group->first_seen_at,
                'last_occurrence_at' => $group->last_seen_at,
                'stack_trace' => $group->latestOccurrence?->stack_trace,
            ],
            'occurrences' => $occurrences->items(),
        ]);
    }

    public function updateStatus(Request $request, Organization $organization, Project $project, string $uuid): JsonResponse
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
