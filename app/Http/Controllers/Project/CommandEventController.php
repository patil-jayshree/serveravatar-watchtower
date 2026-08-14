<?php

declare(strict_types=1);

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\CommandEvent;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CommandEventController extends Controller
{
    /**
     * Display a listing of command events.
     */
    public function index(Request $request, Organization $organization, Project $project): Response
    {
        // Ensure user owns the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $query = $project->commandEvents()
            ->orderByDesc('created_at');

        // Filters
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->status($request->input('status'));
        }

        if ($request->filled('exit_code') && $request->input('exit_code') !== 'all') {
            $exitCode = (int) $request->input('exit_code');
            if ($exitCode === 0) {
                $query->where('exit_code', 0);
            } else {
                $query->where('exit_code', '!=', 0)->where('exit_code', '!=', 0);
            }
        }

        if ($request->filled('environment') && $request->input('environment') !== 'all') {
            $query->where('environment', $request->input('environment'));
        }

        if ($request->filled('time_range') && $request->input('time_range') !== 'all') {
            $query->inTimeRange($request->input('time_range'));
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $commands = $query->paginate($perPage);

        // Get distinct environments for filter
        $environments = $project->commandEvents()
            ->whereNotNull('environment')
            ->distinct()
            ->pluck('environment')
            ->sort()
            ->values();

        // Stats
        $slowThreshold = (int) config('watchtower.command_monitoring.slow_threshold_ms', 1000);
        $stats = [
            'total' => $project->commandEvents()->count(),
            'completed' => $project->commandEvents()->status('completed')->count(),
            'failed' => $project->commandEvents()->status('failed')->count(),
            'slow' => $project->commandEvents()->slow($slowThreshold)->count(),
            'avg_duration' => round($project->commandEvents()->whereNotNull('duration_ms')->avg('duration_ms') ?? 0),
        ];

        // Recent slow commands
        $slowCommands = $project->commandEvents()
            ->slow($slowThreshold)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return Inertia::render('Projects/Commands/Index', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'commands' => $commands->items(),
            'stats' => $stats,
            'filters' => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', 'all'),
                'exit_code' => $request->input('exit_code', 'all'),
                'environment' => $request->input('environment', 'all'),
                'time_range' => $request->input('time_range', '24h'),
            ],
        ]);
    }

    /**
     * Display the specified command event.
     */
    public function show(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        // Ensure user owns the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $command = $project->commandEvents()
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Get related request if available
        $requestEvent = null;
        if ($command->request_id) {
            $requestEvent = $project->requestEvents()
                ->where('request_id', $command->request_id)
                ->first();
        }

        // Get related exception group
        $exceptionGroup = $command->hasException() ? $command->getRelatedExceptionGroup() : null;

        // Get related exception occurrence (the one linked to this command)
        $exceptionOccurrence = null;
        if ($command->hasException()) {
            $exceptionOccurrence = \App\Models\ExceptionOccurrence::where('command_uuid', $command->uuid)
                ->where('project_id', $project->id)
                ->orderByDesc('occurred_at')
                ->first();
        }

        // Related commands (same command name, recent)
        $relatedCommands = $project->commandEvents()
            ->where('command_name', $command->command_name)
            ->where('uuid', '!=', $command->uuid)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return Inertia::render('Projects/Commands/Show', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'command' => $command,
            'exceptionGroup' => $exceptionGroup,
            'exceptionOccurrence' => $exceptionOccurrence,
            'relatedCommands' => $relatedCommands,
        ]);
    }
}
