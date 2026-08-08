<?php

namespace App\Actions\Organization;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UpdateMemberRole
{
    /**
     * Update a member's role.
     *
     * @param Organization $organization
     * @param User $user
     * @param OrganizationRole $role
     */
    public function execute(Organization $organization, User $user, OrganizationRole $role): OrganizationMembership
    {
        $membership = $organization->memberships()->where('user_id', $user->id)->first();

        if (! $membership) {
            throw ValidationException::withMessages([
                'email' => ['This user is not a member of this organization.'],
            ]);
        }

        // Cannot change the owner's role
        if ($membership->isOwner()) {
            throw ValidationException::withMessages([
                'email' => ['Cannot change the role of the organization owner.'],
            ]);
        }

        // Cannot set someone as owner through this action
        if ($role === OrganizationRole::Owner) {
            throw ValidationException::withMessages([
                'role' => ['Cannot assign owner role through this action.'],
            ]);
        }

        $membership->update(['role' => $role]);

        return $membership->fresh();
    }
}
