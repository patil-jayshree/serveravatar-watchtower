<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreCommandEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreCommandEventRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    public function __construct(
        protected StoreCommandEvent $storeCommandEvent
    ) {}

    /**
     * Store a command event from the agent.
     *
     * POST /api/agent/commands
     */
    public function store(StoreCommandEventRequest $request): JsonResponse
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
            Log::info('Command event received', [
                'project_id' => $project->id,
                'command_name' => $validated['command_name'] ?? 'unknown',
                'command_uuid' => $validated['command_uuid'] ?? null,
                'status' => $validated['status'] ?? 'unknown',
            ]);

            $commandEvent = $this->storeCommandEvent->execute($project, $validated);

            return response()->json([
                'success' => true,
                'uuid' => $commandEvent->uuid,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store command event', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store command event.',
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

        $tokenHash = hash('sha256', $tokenValue);

        return \App\Models\AgentToken::where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->first();
    }
}
