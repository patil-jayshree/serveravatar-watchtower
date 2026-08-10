<?php

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreRequestEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreRequestEventRequest;
use App\Models\AgentToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TelemetryController extends Controller
{
    public function storeRequest(StoreRequestEventRequest $request): JsonResponse
    {
        $token = $request->attributes->get('agent_token');

        if (! $token instanceof AgentToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validated();

        // Ensure request_id is unique - if duplicate, generate a new one
        $requestId = $validated['request_id'];
        $existingCount = \App\Models\RequestEvent::where('request_id', $requestId)
            ->where('project_id', $token->project_id)
            ->count();

        if ($existingCount > 0) {
            $requestId = $requestId . '-' . Str::random(8);
        }

        $event = StoreRequestEvent::execute(
            project: $token->project,
            data: array_merge($validated, ['request_id' => $requestId])
        );

        return response()->json([
            'received' => true,
            'uuid' => $event->uuid,
        ], 201);
    }
}
