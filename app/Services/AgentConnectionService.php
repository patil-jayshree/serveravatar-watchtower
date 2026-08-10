<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Agent\AgentTokenStatus;
use App\Models\AgentToken;
use App\Models\Project;

class AgentConnectionService
{
    /**
     * Resolve and authenticate an agent token.\n     *
     * @throws \App\Exceptions\InvalidAgentTokenException
     * @throws \App\Exceptions\RevokedAgentTokenException
     * @throws \App\Exceptions\AgentTokenNotFoundException
     */
    public function resolveToken(string $rawToken): AgentToken
    {
        if (! str_starts_with($rawToken, 'wt_live_')) {
            throw new \App\Exceptions\InvalidAgentTokenException('Invalid token format.');
        }

        // Find all tokens with the matching prefix to verify
        $tokens = AgentToken::where('token_prefix', 'wt_live_')->get();

        $token = null;
        foreach ($tokens as $t) {
            if ($t->verifyHash($rawToken)) {
                $token = $t;
                break;
            }
        }

        if (! $token) {
            throw new \App\Exceptions\InvalidAgentTokenException('Token not found or invalid.');
        }

        // Check if token is revoked
        if ($token->isRevoked()) {
            throw new \App\Exceptions\RevokedAgentTokenException('Token has been revoked.');
        }

        // Check if token is active
        if (! $token->isActive()) {
            throw new \App\Exceptions\InvalidAgentTokenException('Token is not active.');
        }

        return $token;
    }

    /**
     * Verify a connection for a raw token and return project info.
     *
     * @throws \App\Exceptions\InvalidAgentTokenException
     * @throws \App\Exceptions\RevokedAgentTokenException
     */
    public function verifyConnection(string $rawToken): array
    {
        $token = $this->resolveToken($rawToken);
        $project = $token->project;

        // Mark project as connected
        $project->markAsConnected();

        return [
            'success' => true,
            'project' => [
                'id' => $project->uuid,
                'name' => $project->name,
            ],
        ];
    }

    /**
     * Resolve token for telemetry without updating project connection status.
     * Used for high-frequency telemetry endpoints.
     *
     * @throws \App\Exceptions\InvalidAgentTokenException
     * @throws \App\Exceptions\RevokedAgentTokenException
     */
    public function resolveTokenForTelemetry(string $rawToken): AgentToken
    {
        return $this->resolveToken($rawToken);
    }
}
