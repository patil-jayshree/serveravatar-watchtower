<?php

declare(strict_types=1);

namespace App\Actions\Agent;

use App\Enums\Agent\AgentTokenStatus;
use App\Models\Project;

class RevokeAgentToken
{
    /**
     * Revoke the agent token for a project.
     */
    public function execute(Project $project): void
    {
        $project->agentToken()
            ->where('status', AgentTokenStatus::Active)
            ->update([
                'status' => AgentTokenStatus::Revoked,
                'revoked_at' => now(),
            ]);
    }
}
