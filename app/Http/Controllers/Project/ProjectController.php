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
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of the organization's projects.
     */
    public function index(Request $request): View
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

        $projects = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('projects.index', [
            'organization' => $organization,
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
    public function create(Request $request): View
    {
        $organization = $request->attributes->get('organization');

        return view('projects.create', [
            'organization' => $organization,
            'frameworks' => ProjectFramework::cases(),
            'environments' => ProjectEnvironment::cases(),
            'statuses' => ProjectStatus::cases(),
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
    public function show(Request $request): View
    {
        $organization = $request->attributes->get('organization');
        $project = $request->attributes->get('project');

        $timeRange = $request->input('range', '24h');
        if (!in_array($timeRange, ['1h', '24h', '7d', '30d'])) {
            $timeRange = '24h';
        }

        $overviewService = new ProjectOverviewService($project, $timeRange);

        return view('projects.show', [
            'organization' => $organization,
            'project' => $project,
            'overviewService' => $overviewService,
            'timeRange' => $timeRange,
        ]);
    }

    /**
     * Show the form for editing the project.
     */
    public function edit(Request $request): View
    {
        $organization = $request->attributes->get('organization');
        $project = $request->attributes->get('project');

        return view('projects.edit', [
            'organization' => $organization,
            'project' => $project,
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
