<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\PerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    /**
     * Get performance metrics for a project.
     */
    public function show(Request $request, string $organization, string $projectId): JsonResponse
    {
        $project = $this->resolveProject($organization, $projectId);

        if (!$project) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        // Validate time range
        $timeRange = $request->input('range', '24h');
        if (!in_array($timeRange, ['1h', '24h', '7d', '30d'])) {
            $timeRange = '24h';
        }

        $service = new PerformanceService($project, $timeRange);

        if (!$service->hasData()) {
            return response()->json([
                'has_data' => false,
                'time_range' => $service->getTimeRangeLabel(),
                'metrics' => null,
            ]);
        }

        return response()->json([
            'has_data' => true,
            'time_range' => $service->getTimeRangeLabel(),
            'metrics' => $service->getMetrics(),
            'trends' => [
                'response_time' => $service->getResponseTimeTrend(),
                'throughput' => $service->getThroughputTrend(),
                'errors' => $service->getErrorTrend(),
            ],
            'slowest_endpoints' => $service->getSlowestEndpoints(),
            'recent_slow_requests' => $service->getRecentSlowRequests(),
            'sql_contribution' => $service->getSqlContribution(),
            'jobs_context' => $service->getJobsContext(),
        ]);
    }

    /**
     * Resolve project by organization and project identifiers.
     */
    protected function resolveProject(string $organization, string $projectId): ?Project
    {
        // Organization lookup by uuid or id
        $orgQuery = \App\Models\Organization::query();

        // Try uuid first, then id
        $org = $orgQuery->where('uuid', $organization)
            ->orWhere('id', (int) $organization)
            ->first();

        if (!$org) {
            return null;
        }

        // Ensure user owns the organization
        if ($org->user_id !== Auth::id()) {
            return null;
        }

        // Project lookup by uuid or id
        return Project::where('organization_id', $org->id)
            ->where(function ($q) use ($projectId) {
                $q->where('uuid', $projectId)
                    ->orWhere('id', (int) $projectId);
            })
            ->first();
    }
}
