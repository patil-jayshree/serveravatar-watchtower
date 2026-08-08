<?php

namespace App\Actions\Organization;

use App\Models\Organization;

class UpdateOrganization
{
    /**
     * Update the organization.
     *
     * @param Organization $organization
     * @param array{name?: string, logo_path?: string|null} $data
     */
    public function execute(Organization $organization, array $data): Organization
    {
        $organization->update($data);

        return $organization->fresh();
    }
}
