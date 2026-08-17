<?php

namespace App\Http\Controllers\Project;

use App\Actions\Project\CreateProject;
use App\Services\ProjectOverviewService;
use App\Actions\Project\DeleteProject;
use App\Actions\Project\UpdateProject;
use App\Enums\Project\ProjectEnvironment;
use App\Enums\Project\ProjectFramework;
use App\Enums\Project\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the organization's projects.
     */
    public function index(Request $request): Response
    {
        $organization = $request->attributes->get('organization');

        // Get filter parameters
        $search = $request->query('search');
        $environment = $request->query('environment');
        $framework = $request->query('framework');
        $status = $request->query('status');

        // Build query
        $query = $organization->projects()->where(function ($q) use ($search, $environment, $framework, $status) {
            if ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            }

            if ($environment && ProjectEnvironment::tryFrom($environment)) {
                $q->where('environment', $environment);
            }

            if ($framework && ProjectFramework::tryFrom($framework)) {
                $q->where('framework', $framework);
            }

            if ($status && ProjectStatus::tryFrom($status)) {
                $q->where('status', $status);
            }
        });

        $projects = $query->orderBy('created_at', 'desc')->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'environment' => $p->environment,
            'framework' => $p->framework,
            'status' => $p->status,
            'is_agent_connected' => $p->is_agent_connected,
            'environments_count' => 1,
            'created_at' => $p->created_at,
            'stats' => [
                'requests_24h' => 0,
                'errors_24h' => 0,
                'avg_response' => '0ms',
            ],
        ]);

        return Inertia::render('Projects/Index', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'projects' => $projects,
            'filters' => [
                'search' => $search,
                'environment' => $environment,
                'framework' => $framework,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(Request $request): Response
    {
        $organization = $request->attributes->get('organization');

        // If organization is set (from org-scoped route), don't show dropdown
        // If not set (from global route), show organization dropdown
        if ($organization) {
            return Inertia::render('Projects/Create', [
                'organization' => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                ],
                'organizations' => null,
            ]);
        }

        // Global create - get all organizations for dropdown
        $organizations = Auth::user()->organizations()->get()->map(fn($org) => [
            'id' => $org->id,
            'name' => $org->name,
        ]);

        return Inertia::render('Projects/Create', [
            'organization' => null,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(CreateProjectRequest $request, CreateProject $action): RedirectResponse
    {
        $organization = $request->attributes->get('organization');
        $project = $action->execute($organization, $request->validated());

        return redirect()->route('organizations.projects.show', [$organization, $project])
            ->with('status', 'Project created successfully.');
    }

    /**
     * Display the project's overview.
     */
    public function show(Request $request): Response
    {
        $organization = $request->attributes->get('organization');
        $project = $request->attributes->get('project');

        $timeRange = $request->input('range', '24h');
        if (!in_array($timeRange, ['1h', '24h', '7d', '30d'])) {
            $timeRange = '24h';
        }

        return Inertia::render('Projects/Show', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'environment' => $project->environment,
                'framework' => $project->framework,
                'status' => $project->status,
                'is_agent_connected' => $project->is_agent_connected,
                'created_at' => $project->created_at,
            ],
            'stats' => [
                'requests_24h' => 0,
                'errors_24h' => 0,
                'avg_response' => '0ms',
                'slow_queries' => 0,
            ],
            'timeRange' => $timeRange,
        ]);
    }

    /**
     * Show the form for editing the project.
     */
    public function edit(Request $request): Response
    {
        $organization = $request->attributes->get('organization');
        $project = $request->attributes->get('project');

        return Inertia::render('Projects/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'uuid' => $project->uuid,
                'name' => $project->name,
                'description' => $project->description,
                'framework' => $project->framework,
                'environment' => $project->environment,
                'status' => $project->status,
            ],
            'frameworks' => ProjectFramework::cases(),
            'environments' => ProjectEnvironment::cases(),
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    /**
     * Update the project.
     */
    public function update(UpdateProjectRequest $request, UpdateProject $action): RedirectResponse
    {
        $project = $request->attributes->get('project');
        $organization = $request->attributes->get('organization');

        $action->execute($project, $request->validated());

        return redirect()->route('organizations.projects.show', [$organization, $project])
            ->with('status', 'Project updated successfully.');
    }

    /**
     * Remove the project.
     */
    public function destroy(Request $request, DeleteProject $action): RedirectResponse
    {
        $project = $request->attributes->get('project');
        $organization = $request->attributes->get('organization');

        $action->execute($project);

        return redirect()->route('organizations.projects.index', $organization)
            ->with('status', 'Project deleted successfully.');
    }
}
