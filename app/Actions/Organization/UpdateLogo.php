<?php

namespace App\Actions\Organization;

use App\Models\Organization;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateLogo
{
    /**
     * Update the organization logo.
     *
     * @param Organization $organization
     * @param UploadedFile|null $file
     */
    public function execute(Organization $organization, ?UploadedFile $file): Organization
    {
        // Delete old logo if exists
        if ($organization->logo_path) {
            Storage::disk('avatars')->delete($organization->logo_path);
        }

        // If no new file, just remove the logo_path
        if (! $file) {
            $organization->update(['logo_path' => null]);
            return $organization->fresh();
        }

        // Generate unique filename
        $filename = 'org_' . $organization->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Store the new logo
        $file->storeAs('/', $filename, ['disk' => 'avatars']);

        // Update organization
        $organization->update(['logo_path' => $filename]);

        return $organization->fresh();
    }
}
