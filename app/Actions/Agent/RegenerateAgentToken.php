<?php

declare(strict_types=1);

namespace App\Actions\Agent;

use App\Enums\Agent\AgentTokenStatus;
use App\Models\AgentToken;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class RegenerateAgentToken
{
    /**
     * Regenerate a new agent token for a project.
     * Invalidates the old token immediately.
     *
     * @return array{token: string, masked: string, status: string}
     */
    public function execute(Project $project): array
    {
        return DB::transaction(function () use ($project) {
            // Revoke existing active token if any
            $project->agentToken()
                ->where('status', AgentTokenStatus::Active)
                ->update([
                    'status' => AgentTokenStatus::Revoked,
                    'revoked_at' => now(),
                ]);

            // Generate new token
            $tokenData = AgentToken::generateToken();

            $agentToken = $project->agentToken()->create([
                'token_prefix' => $tokenData['prefix'],
                'token_hash' => $tokenData['hash'],
                'status' => AgentTokenStatus::Active,
            ]);

            return [
                'token' => $tokenData['token'], // Only returned once at generation
                'masked' => $agentToken->masked_token,
                'status' => $agentToken->status->value,
            ];
        });
    }
}
