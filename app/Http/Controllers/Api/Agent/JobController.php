<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreJobEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreJobEventRequest;
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
        $validated = $request->validated();

        // Get the agent token and resolve the project
        $token = $this->getAgentToken($request);

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing agent token.',
            ], 401);
        }

        $project = $token->project;

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
            ], 404);
        }

        try {
            \Illuminate\Support\Facades\Log::info('Job event received', [
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

    /**
     * Get the agent token from the request.
     */
    protected function getAgentToken($request): ?\App\Models\AgentToken
    {
        $tokenValue = $request->input('token')
            ?? $request->header('X-Agent-Token')
            ?? $request->bearerToken();

        if (! $tokenValue) {
            return null;
        }

        // Look up by hashed token value (AgentToken stores hash, not raw token)
        $tokenHash = hash('sha256', $tokenValue);

        return \App\Models\AgentToken::where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->first();
    }
}
