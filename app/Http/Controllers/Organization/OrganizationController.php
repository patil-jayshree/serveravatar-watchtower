<?php

namespace App\Http\Controllers\Organization;

use App\Actions\Organization\CreateOrganization;
use App\Actions\Organization\UpdateLogo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the user's organizations.
     */
    public function index(Request $request): Response
    {
        $perPage = (int) $request->get('per_page', 4);
        $orgsPaginated = Auth::user()->organizations()->paginate($perPage);

        $organizations = $orgsPaginated->map(function ($org) {
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
                'slug' => $org->slug ?? Str::slug($org->name),
                'description' => $org->description,
                'logo_url' => $org->logo_url ?? $org->default_logo_url,
                'status' => $total > 0 ? 'active' : 'inactive',
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
            'orgs_pagination' => [
                'current_page' => $orgsPaginated->currentPage(),
                'last_page' => $orgsPaginated->lastPage(),
                'per_page' => $orgsPaginated->perPage(),
                'total' => $orgsPaginated->total(),
            ],
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

        $perPage = (int) $request->get('per_page', 4);
        $projectsPaginated = $organization->projects()->paginate($perPage);

        $projects = $projectsPaginated->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'environment' => $p->environment,
            'framework' => $p->framework,
            'status' => $p->status,
            'is_agent_connected' => $p->is_agent_connected,
            'created_at' => $p->created_at?->format('M d, Y'),
        ]);

        return Inertia::render('Organizations/Show', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'logo_url' => $organization->logo_url,
                'created_at' => $organization->created_at?->format('M d, Y'),
            ],
            'stats' => [
                'total_projects' => $organization->projects()->count(),
                'total_requests' => 0,
                'total_errors' => 0,
                'avg_response_time' => '0ms',
            ],
            'projects' => $projects,
            'projects_pagination' => [
                'current_page' => $projectsPaginated->currentPage(),
                'last_page' => $projectsPaginated->lastPage(),
                'per_page' => $projectsPaginated->perPage(),
                'total' => $projectsPaginated->total(),
            ],
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
        $validated = $request->validated();

        // Remove logo from validated data (we handle it separately)
        unset($validated['logo']);

        // Create organization
        $organization = $action->execute(Auth::user(), $validated);

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $logoAction = app(UpdateLogo::class);
            $logoAction->execute($organization, $request->file('logo'));
        }

        return redirect()->route('organizations.index')->with('status', 'Organization created successfully.');
    }

    /**
     * Show the form for editing the organization.
     */
    public function edit(Request $request): Response
    {
        $organization = $request->attributes->get('organization');

        return Inertia::render('Organizations/Index', [
            'editingOrganization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'description' => $organization->description,
                'logo_url' => $organization->logo_url,
            ],
        ]);
    }

    /**
     * Update the organization.
     */
    public function update(Request $request): RedirectResponse
    {
        $organization = $request->attributes->get('organization');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle logo upload using UpdateLogo action
        if ($request->hasFile('logo')) {
            $logoAction = app(UpdateLogo::class);
            $logoAction->execute($organization, $request->file('logo'));
        }

        $organization->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back();
    }

    /**
     * Delete the organization.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $organization = $request->attributes->get('organization');

        $organization->delete();

        return redirect()->route('organizations.index')->with('status', 'Organization deleted successfully.');
    }
}
