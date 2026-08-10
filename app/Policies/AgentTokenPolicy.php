<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AgentToken;
use App\Models\Project;
use App\Models\User;

class AgentTokenPolicy
{
    /**
     * Determine whether the user can view the agent token.
     */
    public function view(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user);
    }

    /**
     * Determine whether the user can generate an agent token.
     */
    public function create(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user);
    }

    /**
     * Determine whether the user can regenerate an agent token.
     */
    public function regenerate(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user);
    }

    /**
     * Determine whether the user can revoke an agent token.
     */
    public function revoke(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user);
    }
}
