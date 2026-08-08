<?php

namespace App\Actions\Organization;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateOrganization
{
    /**
     * Create a new organization.
     *
     * @param User $user
     * @param array{name: string, logo_path?: string} $data
     */
    public function execute(User $user, array $data): Organization
    {
        return DB::transaction(function () use ($user, $data) {
            // Create the organization
            $organization = Organization::create([
                'name' => $data['name'],
                'logo_path' => $data['logo_path'] ?? null,
                'owner_id' => $user->id,
                'status' => 'active',
            ]);

            // Add the creator as an owner member
            $organization->memberships()->create([
                'user_id' => $user->id,
                'role' => OrganizationRole::Owner,
            ]);

            return $organization;
        });
    }
}
