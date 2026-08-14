<?php

declare(strict_types=1);

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\CommandEvent;
use App\Models\ExceptionOccurrence;
use App\Models\JobEvent;
use App\Models\Organization;
use App\Models\Project;
use App\Models\SchedulerExecution;
use App\Models\SchedulerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SchedulerController extends Controller
{
    /**
     * Display a listing of scheduler tasks.
     */
    public function index(Request $request, Organization $organization, Project $project): Response
    {
        // Ensure user owns the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $query = $project->schedulerTasks()
            ->orderBy('task_name');

        // Filters
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->status($request->input('status'));
        }

        if ($request->filled('environment') && $request->input('environment') !== 'all') {
            $query->environment($request->input('environment'));
        }

        if ($request->filled('time_range') && $request->input('time_range') !== 'all') {
            $query->inTimeRange($request->input('time_range'));
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $tasks = $query->paginate($perPage);

        // Stats
        $stats = [
            'total' => $project->schedulerTasks()->count(),
            'healthy' => $project->schedulerTasks()->status('completed')->count(),
            'running' => $project->schedulerTasks()->status('running')->count(),
            'failed' => $project->schedulerTasks()->status('failed')->count(),
            'missed' => $project->schedulerTasks()->status('missed')->count(),
        ];

        // Get distinct environments for filter
        $environments = $project->schedulerTasks()
            ->whereNotNull('environment')
            ->distinct()
            ->pluck('environment')
            ->sort()
            ->values();

        return Inertia::render('Projects/Scheduler/Index', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'tasks' => $tasks->items(),
            'executions' => [],
            'stats' => $stats,
            'filters' => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', 'all'),
                'environment' => $request->input('environment', 'all'),
                'time_range' => $request->input('time_range', '24h'),
            ],
        ]);
    }

    /**
     * Display the specified scheduler task with execution history.
     */
    public function show(Request $request, Organization $organization, Project $project, string $uuid): Response
    {
        // Ensure user owns the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403);
        }

        $task = $project->schedulerTasks()
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Execution history for this task
        $executionsQuery = $project->schedulerExecutions()
            ->where('scheduler_task_uuid', $task->uuid)
            ->with('exceptionOccurrence.exceptionGroup')
            ->orderByDesc('created_at');

        if ($request->filled('time_range') && $request->input('time_range') !== 'all') {
            $executionsQuery->inTimeRange($request->input('time_range'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $executionsQuery->status($request->input('status'));
        }

        $perPage = min((int) $request->input('per_page', 25), 100);
        $executions = $executionsQuery->paginate($perPage);

        // Execution stats
        $executionStats = [
            'total' => $project->schedulerExecutions()->where('scheduler_task_uuid', $task->uuid)->count(),
            'completed' => $project->schedulerExecutions()->where('scheduler_task_uuid', $task->uuid)->status('completed')->count(),
            'failed' => $project->schedulerExecutions()->where('scheduler_task_uuid', $task->uuid)->status('failed')->count(),
            'missed' => $project->schedulerExecutions()->where('scheduler_task_uuid', $task->uuid)->status('missed')->count(),
            'avg_duration' => round((float) ($project->schedulerExecutions()
                ->where('scheduler_task_uuid', $task->uuid)
                ->whereNotNull('duration_ms')
                ->avg('duration_ms') ?? 0)),
        ];

        // Related command if applicable
        $commandEvent = null;
        if ($task->command_name) {
            $commandEvent = CommandEvent::where('project_id', $project->id)
                ->where('command_name', $task->command_name)
                ->latest('created_at')
                ->first();
        }

        // Related job if applicable
        $jobEvent = null;
        if ($task->job_uuid) {
            $jobEvent = JobEvent::where('project_id', $project->id)
                ->where('uuid', $task->job_uuid)
                ->first();
        }

        // Related exception if the latest execution failed
        $exceptionOccurrence = null;
        $latestExecution = $task->latestExecution;
        if ($latestExecution && $latestExecution->status === 'failed' && $latestExecution->exception_uuid) {
            $exceptionOccurrence = ExceptionOccurrence::with('exceptionGroup')
                ->where('uuid', $latestExecution->exception_uuid)
                ->where('project_id', $project->id)
                ->first();
        }

        return Inertia::render('Projects/Scheduler/Show', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'task' => $task,
            'executions' => $executions->items(),
            'executionStats' => $executionStats,
            'commandEvent' => $commandEvent,
            'jobEvent' => $jobEvent,
            'exceptionOccurrence' => $exceptionOccurrence,
            'filters' => [
                'status' => $request->input('status', 'all'),
                'time_range' => $request->input('time_range', '30d'),
            ],
        ]);
    }
}
