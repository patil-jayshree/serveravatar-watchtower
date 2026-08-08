<?php

namespace App\Actions\Organization;

use App\Models\Organization;
use Illuminate\Support\Facades\Storage;

class DeleteOrganization
{
    /**
     * Delete the organization.
     */
    public function execute(Organization $organization): bool
    {
        // Delete the logo if exists
        if ($organization->logo_path) {
            Storage::disk('avatars')->delete($organization->logo_path);
        }

        return $organization->delete();
    }
}
