<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreQueryEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreQueryEventRequest;
use App\Models\AgentToken;
use Illuminate\Http\JsonResponse;

class QueryController extends Controller
{
    public function __construct(
        protected StoreQueryEvent $storeQueryEvent
    ) {}

    /**
     * Store query telemetry from the agent.
     */
    public function store(StoreQueryEventRequest $request): JsonResponse
    {
        $token = $request->attributes->get('agent_token');

        if (! $token instanceof AgentToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $queryEvent = $this->storeQueryEvent->execute(
            project: $token->project,
            data: $request->validated()
        );

        return response()->json([
            'received' => true,
            'uuid' => $queryEvent->uuid,
        ], 201);
    }
}
