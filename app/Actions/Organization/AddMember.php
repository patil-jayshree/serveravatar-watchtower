<?php

namespace App\Actions\Organization;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AddMember
{
    /**
     * Add a member to the organization.
     *
     * @param Organization $organization
     * @param User $user
     * @param OrganizationRole $role
     */
    public function execute(Organization $organization, User $user, OrganizationRole $role = OrganizationRole::Member): OrganizationMembership
    {
        // Check if user is already a member
        if ($organization->hasMember($user)) {
            throw ValidationException::withMessages([
                'email' => ['This user is already a member of this organization.'],
            ]);
        }

        return $organization->memberships()->create([
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }
}
