<?php

namespace App\Http\Controllers\Organization;

use App\Actions\Organization\CreateOrganization;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the user's organizations.
     */
    public function index(): Response
    {
        $organizations = Auth::user()->organizations()->get()->map(function ($org) {
            $projects = $org->projects;
            $total = $projects->count();
            $connected = $projects->where('is_connected', true)->count();

            $healthy = 0;
            $warning = 0;
            $critical = 0;

            foreach ($projects->where('is_connected', true) as $project) {
                $health = $this->calculateProjectHealth($project);
                if ($health === 'healthy') $healthy++;
                elseif ($health === 'warning') $warning++;
                elseif ($health === 'critical') $critical++;
            }

            return [
                'id' => $org->id,
                'name' => $org->name,
                'logo_url' => $org->logo_url,
                'status' => $connected > 0 ? 'active' : 'inactive',
                'created_at' => $org->created_at?->format('M d, Y'),
                'projects_count' => $total,
                'stats' => [
                    'healthy' => $healthy,
                    'warning' => $warning,
                    'critical' => $critical,
                ],
            ];
        });

        return Inertia::render('Organizations/Index', [
            'organizations' => $organizations,
        ]);
    }

    /**
     * Calculate project health.
     */
    private function calculateProjectHealth($project): string
    {
        $errorCount = $project->exceptionGroups()
            ->whereIn('status', ['open', 'new'])
            ->count();

        $failedJobs = $project->jobEvents()
            ->where('status', 'failed')
            ->count();

        $failedCommands = $project->commandEvents()
            ->where('exit_code', '!=', 0)
            ->count();

        if ($errorCount > 10 || $failedJobs > 5 || $failedCommands > 5) {
            return 'critical';
        }

        if ($errorCount > 0 || $failedJobs > 0 || $failedCommands > 0) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Display the organization's overview.
     */
    public function show(Request $request): Response
    {
        $organization = $request->attributes->get('organization');

        return Inertia::render('Organizations/Show', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'logo_url' => $organization->logo_url,
                'created_at' => $organization->created_at,
            ],
            'stats' => [
                'total_projects' => $organization->projects()->count(),
                'total_requests' => 0,
                'total_errors' => 0,
                'avg_response_time' => '0ms',
            ],
            'recentProjects' => [],
        ]);
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create(): Response
    {
        return Inertia::render('Organizations/Create');
    }

    /**
     * Store a newly created organization.
     */
    public function store(CreateOrganizationRequest $request, CreateOrganization $action): RedirectResponse
    {
        $organization = $action->execute(Auth::user(), $request->validated());

        return redirect()->route('organizations.show', $organization)->with('status', 'Organization created successfully.');
    }
}
