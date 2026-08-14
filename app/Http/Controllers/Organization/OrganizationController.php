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
        $organizations = Auth::user()->organizations()->get()->map(fn($org) => [
            'id' => $org->id,
            'name' => $org->name,
            'logo_url' => $org->logo_url,
            'projects_count' => $org->projects()->count(),
        ]);

        return Inertia::render('Organizations/Index', [
            'organizations' => $organizations,
        ]);
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
                'total_requests' => 0, // Will be populated from events
                'total_errors' => 0,
                'avg_response_time' => '0ms',
            ],
            'recentProjects' => [], // Will be populated
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
