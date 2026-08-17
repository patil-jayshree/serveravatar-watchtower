<?php

namespace App\Http\Controllers;

use App\Services\ProjectOverviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class GlobalProjectController extends Controller
{
    /**
     * Display all projects across all organizations.
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        $projects = Auth::user()
            ->organizations()
            ->with(['projects'])
            ->get()
            ->pluck('projects')
            ->flatten()
            ->when($search, fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'environment' => $project->environment,
                    'framework' => $project->framework,
                    'status' => $project->status,
                    'is_agent_connected' => $project->is_agent_connected,
                    'organization_id' => $project->organization_id,
                    'organization_name' => $project->organization?->name,
                    'created_at' => $project->created_at?->format('M d, Y'),
                ];
            });

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'isGlobal' => true,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }
}
