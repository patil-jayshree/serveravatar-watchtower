<?php

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreLogEventRequest;
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
        // Get agent token and resolve project
        $agentToken = $this->getAgentToken();
        if (!$agentToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing agent token.',
            ], 401);
        }

        $project = $agentToken->project;
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found for this token.',
            ], 404);
        }

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

    /**
     * Get the agent token from the request.
     */
    protected function getAgentToken(): ?\App\Models\AgentToken
    {
        $token = $this->getTokenFromRequest();
        if (!$token) {
            return null;
        }

        // Look up by hashed token value (AgentToken stores hash, not raw token)
        $tokenHash = hash('sha256', $token);

        return \App\Models\AgentToken::where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->first();
    }

    /**
     * Extract the token from the Authorization header.
     */
    protected function getTokenFromRequest(): ?string
    {
        $header = request()->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        // Also check X-Agent-Token header
        $header = request()->header('X-Agent-Token', '');
        if ($header) {
            return $header;
        }

        // Fallback to query parameter for testing
        return request()->query('token', request()->input('token'));
    }
}
