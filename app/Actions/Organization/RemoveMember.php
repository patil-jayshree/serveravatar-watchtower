<?php

namespace App\Actions\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RemoveMember
{
    /**
     * Remove a member from the organization.
     */
    public function execute(Organization $organization, User $user): bool
    {
        $membership = $organization->memberships()->where('user_id', $user->id)->first();

        if (! $membership) {
            throw ValidationException::withMessages([
                'email' => ['This user is not a member of this organization.'],
            ]);
        }

        // Cannot remove the owner
        if ($membership->isOwner()) {
            throw ValidationException::withMessages([
                'email' => ['Cannot remove the organization owner.'],
            ]);
        }

        return $membership->delete();
    }
}
