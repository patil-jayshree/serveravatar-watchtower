<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Organization\SwitchOrganizationController;
use App\Models\Organization;
use App\Services\DashboardAggregationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        $user = Auth::user();
        $organizations = $user->organizations()->get();

        // Handle no organizations - show welcome page
        if ($organizations->isEmpty()) {
            return Inertia::render('Welcome', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        // Auto-select if only one organization exists
        if ($organizations->count() === 1) {
            $selectedOrg = $organizations->first();
            session(['selected_organization_id' => $selectedOrg->id]);
        } else {
            $selectedOrgId = session('selected_organization_id');

            if ($selectedOrgId) {
                $selectedOrg = $organizations->find($selectedOrgId);
                if (!$selectedOrg) {
                    session()->forget('selected_organization_id');
                    $selectedOrg = null;
                }
            } else {
                $selectedOrg = null;
            }
        }

        // If multiple orgs but none selected, auto-select the org with most projects
        if (!$selectedOrg && $organizations->count() > 1) {
            $selectedOrg = $user->organizations()->withCount('projects')->get()->sortByDesc('projects_count')->first();
            session(['selected_organization_id' => $selectedOrg->id]);
        }

        // Get time range from request or default
        $timeRange = $request->input('range', '24h');
        if (!in_array($timeRange, ['1h', '24h', '7d', '30d'])) {
            $timeRange = '24h';
        }

        // Get dashboard aggregation data
        $dashboardService = new DashboardAggregationService($selectedOrg, $timeRange);
        $dashboardData = $dashboardService->getDashboardData();

        // Get global stats across all organizations
        $globalStats = [
            'total_organizations' => $organizations->count(),
            'total_projects' => $organizations->sum(fn($org) => $org->projects()->count()),
            'connected_projects' => $organizations->sum(fn($org) => $org->projects()->where('is_connected', true)->count()),
        ];

        return Inertia::render('Dashboard', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'organizations' => $organizations->map(fn($org) => [
                'id' => $org->id,
                'name' => $org->name,
                'logo_url' => $org->logo_url,
            ]),
            'selectedOrg' => $selectedOrg ? [
                'id' => $selectedOrg->id,
                'name' => $selectedOrg->name,
                'logo_url' => $selectedOrg->logo_url,
            ] : null,
            'globalStats' => $globalStats,
            'dashboardData' => $dashboardData,
            'timeRange' => $timeRange,
        ]);
    }

    /**
     * Handle the dashboard redirect.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }
}
