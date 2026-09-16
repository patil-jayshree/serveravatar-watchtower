<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreCommandEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreCommandEventRequest;
use App\Models\AgentToken;
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
        $token = $request->attributes->get('agent_token');

        if (! $token instanceof AgentToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validated();
        $project = $token->project;

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
}
