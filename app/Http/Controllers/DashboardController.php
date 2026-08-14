<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Organization\SwitchOrganizationController;
use App\Models\Organization;
use App\Services\DashboardAggregationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $organizations = $user->organizations()->get();

        // Handle no organizations
        if ($organizations->isEmpty()) {
            return view('dashboard', [
                'user' => $user,
                'organizations' => $organizations,
                'selectedOrg' => null,
                'dashboardData' => null,
                'timeRange' => '24h',
            ]);
        }

        // Auto-select if only one organization exists
        if ($organizations->count() === 1) {
            $selectedOrg = $organizations->first();
            session(['selected_organization_id' => $selectedOrg->id]);
        } else {
            // Try to use session, or redirect to org selection
            $selectedOrgId = session('selected_organization_id');

            if ($selectedOrgId) {
                $selectedOrg = $organizations->find($selectedOrgId);
                if (!$selectedOrg) {
                    // Invalid session, clear it
                    session()->forget('selected_organization_id');
                    $selectedOrg = null;
                }
            } else {
                $selectedOrg = null;
            }
        }

        // If multiple orgs but none selected, show org selector
        if (!$selectedOrg && $organizations->count() > 1) {
            return view('dashboard', [
                'user' => $user,
                'organizations' => $organizations,
                'selectedOrg' => null,
                'dashboardData' => null,
                'timeRange' => '24h',
            ]);
        }

        // Get time range from request or default
        $timeRange = $request->input('range', '24h');
        if (!in_array($timeRange, ['1h', '24h', '7d', '30d'])) {
            $timeRange = '24h';
        }

        // Get dashboard aggregation data
        $dashboardService = new DashboardAggregationService($selectedOrg, $timeRange);
        $dashboardData = $dashboardService->getDashboardData();

        return view('dashboard', [
            'user' => $user,
            'organizations' => $organizations,
            'selectedOrg' => $selectedOrg,
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
