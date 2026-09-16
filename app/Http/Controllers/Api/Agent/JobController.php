<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreJobEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreJobEventRequest;
use App\Models\AgentToken;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    public function __construct(
        protected StoreJobEvent $storeJobEvent
    ) {}

    /**
     * Debug: store a job event from the agent.
     */
    public function debug(StoreJobEventRequest $request): \Illuminate\Http\JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('Debug job event received', $request->validated());
        return response()->json(['success' => true, 'data' => $request->validated()]);
    }

    /**
     * Store a job event from the agent.
     *
     * POST /api/agent/jobs
     */
    public function store(StoreJobEventRequest $request): JsonResponse
    {
        $token = $request->attributes->get('agent_token');

        if (! $token instanceof AgentToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validated();
        $project = $token->project;

        try {
            Log::info('Job event received', [
                'project_id' => $project->id,
                'event_type' => $validated['event_type'] ?? 'unknown',
                'job_name' => $validated['job_name'] ?? 'unknown',
                'job_uuid' => $validated['job_uuid'] ?? null,
            ]);

            $jobEvent = $this->storeJobEvent->execute($project, $validated);

            return response()->json([
                'success' => true,
                'uuid' => $jobEvent->uuid,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store job event', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store job event.',
            ], 500);
        }
    }
}
