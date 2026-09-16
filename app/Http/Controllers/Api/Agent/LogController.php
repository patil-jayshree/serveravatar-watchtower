<?php

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreLogEventRequest;
use App\Models\AgentToken;
use Illuminate\Http\JsonResponse;

class LogController extends Controller
{
    public function __construct(
        protected StoreLogEvent $storeLogEvent
    ) {}

    /**
     * Store a log event from the agent.
     *
     * POST /api/agent/logs
     */
    public function store(StoreLogEventRequest $request): JsonResponse
    {
        $token = $request->attributes->get('agent_token');

        if (! $token instanceof AgentToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $project = $token->project;

        try {
            $data = $request->validated();
            $data['project_id'] = $project->id;

            $logEvent = $this->storeLogEvent->execute($data);

            return response()->json([
                'success' => true,
                'data' => [
                    'uuid' => $logEvent->uuid,
                    'level' => $logEvent->level,
                    'message' => $logEvent->message,
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store log event.',
            ], 500);
        }
    }
}
