<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Agent;

use App\Exceptions\InvalidAgentTokenException;
use App\Exceptions\RevokedAgentTokenException;
use App\Http\Controllers\Controller;
use App\Services\AgentConnectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    public function __construct(
        private readonly AgentConnectionService $connectionService
    ) {}

    /**
     * Verify agent connection.
     *
     * POST /api/agent/connection
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $rawToken = $request->input('token');

        try {
            $result = $this->connectionService->verifyConnection($rawToken);

            return response()->json($result, 200);
        } catch (InvalidAgentTokenException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        } catch (RevokedAgentTokenException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has been revoked.',
            ], 401);
        }
    }
}
