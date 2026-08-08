<?php

namespace App\Policies;

use App\Enums\OrganizationRole;
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
        return $organization->hasMember($user);
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
        return $organization->userHasRole($user, OrganizationRole::Admin);
    }

    /**
     * Determine whether the user can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $organization->userHasRole($user, OrganizationRole::Owner);
    }

    /**
     * Determine whether the user can manage members.
     */
    public function manageMembers(User $user, Organization $organization): bool
    {
        return $organization->userHasRole($user, OrganizationRole::Admin);
    }

    /**
     * Determine whether the user can update member roles.
     */
    public function updateMemberRole(User $user, Organization $organization): bool
    {
        return $organization->userHasRole($user, OrganizationRole::Admin);
    }

    /**
     * Determine whether the user can remove members.
     */
    public function removeMember(User $user, Organization $organization): bool
    {
        return $organization->userHasRole($user, OrganizationRole::Admin);
    }

    /**
     * Determine whether the user can add members.
     */
    public function addMember(User $user, Organization $organization): bool
    {
        return $organization->userHasRole($user, OrganizationRole::Admin);
    }
}
