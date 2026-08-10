<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects of the organization.
     */
    public function viewAny(User $user, Organization $organization): bool
    {
        return $organization->user_id === $user->id;
    }

    /**
     * Determine whether the user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        return $project->organization->user_id === $user->id;
    }

    /**
     * Determine whether the user can create a project in the organization.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $organization->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->organization->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->organization->user_id === $user->id;
    }
}
