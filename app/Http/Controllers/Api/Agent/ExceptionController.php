<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreExceptionRequest;
use App\Models\AgentToken;
use Illuminate\Http\JsonResponse;

class ExceptionController extends Controller
{
    public function __construct(
        protected StoreException $storeException
    ) {}

    /**
     * Store exception telemetry from the agent.
     */
    public function store(StoreExceptionRequest $request): JsonResponse
    {
        $token = $request->attributes->get('agent_token');

        if (! $token instanceof AgentToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->storeException->execute($token->project, $request->validated());

        return response()->json([
            'received' => true,
            'group_uuid' => $result['group_uuid'],
            'occurrence_uuid' => $result['occurrence_uuid'],
        ], 201);
    }
}
