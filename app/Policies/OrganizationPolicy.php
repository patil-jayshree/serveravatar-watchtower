<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view any organizations.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the organization.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $organization->user_id === $user->id;
    }

    /**
     * Determine whether the user can create an organization.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the organization.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $organization->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $organization->user_id === $user->id;
    }
}
