<?php

declare(strict_types=1);

namespace App\Actions\Agent;

use App\Enums\Agent\AgentTokenStatus;
use App\Models\AgentToken;
use App\Models\Project;

class GenerateAgentToken
{
    /**
     * Generate a new agent token for a project.
     *
     * @return array{token: string, masked: string, status: string}
     */
    public function execute(Project $project): array
    {
        // If an active token already exists, return it (don't create duplicates)
        $existingToken = $project->agentToken()
            ->where('status', AgentTokenStatus::Active)
            ->first();

        if ($existingToken) {
            return [
                'token' => null, // Don't expose raw token again
                'masked' => $existingToken->masked_token,
                'status' => $existingToken->status->value,
            ];
        }

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
    }
}
