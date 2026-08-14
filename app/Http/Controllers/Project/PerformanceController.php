<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\PerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PerformanceController extends Controller
{
    /**
     * Display the performance page.
     */
    public function index(Request $request, string $organization, string $projectId): \Illuminate\Http\Response
    {
        $project = $this->resolveProject($organization, $projectId);

        if (!$project) {
            abort(404, 'Project not found.');
        }

        $timeRange = $request->input('range', '24h');
        if (!in_array($timeRange, ['1h', '24h', '7d', '30d'])) {
            $timeRange = '24h';
        }

        $service = new PerformanceService($project, $timeRange);

        return Inertia::render('Projects/Performance/Index', [
            'organization' => [
                'id' => $project->organization->id,
                'name' => $project->organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'metrics' => [
                'avg_response_time' => '0ms',
                'requests_per_second' => 0,
                'error_rate' => '0',
                'cpu_usage' => '0',
            ],
            'timeRange' => $timeRange,
        ]);
    }

    /**
     * Resolve project by organization and project identifiers.
     */
    protected function resolveProject(string $organization, string $projectId): ?Project
    {
        $org = \App\Models\Organization::where('uuid', $organization)
            ->orWhere('id', (int) $organization)
            ->first();

        if (!$org) {
            return null;
        }

        if ($org->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        return Project::where('organization_id', $org->id)
            ->where(function ($q) use ($projectId) {
                $q->where('uuid', $projectId)
                    ->orWhere('id', (int) $projectId);
            })
            ->first();
    }
}
