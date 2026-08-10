<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidAgentTokenException;
use App\Exceptions\RevokedAgentTokenException;
use App\Services\AgentConnectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAgentToken
{
    public function __construct(
        private readonly AgentConnectionService $connectionService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $purpose = null): Response
    {
        $rawToken = $request->input('token') ?? $request->bearerToken();

        if (empty($rawToken)) {
            return response()->json(['error' => 'Token required'], 401);
        }

        try {
            $result = $this->connectionService->verifyConnection($rawToken);

            // Store the token and project on the request for later use
            $request->attributes->set('agent_token', $result['agent_token']);
            $request->attributes->set('agent_project', $result['project']);

            return $next($request);
        } catch (InvalidAgentTokenException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (RevokedAgentTokenException $e) {
            return response()->json(['error' => 'Token has been revoked.'], 401);
        }
    }
}
