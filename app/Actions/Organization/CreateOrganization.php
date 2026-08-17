<?php

namespace App\Actions\Organization;

use App\Models\Organization;
use App\Models\User;

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
        return Organization::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? null,
            'description' => $data['description'] ?? null,
            'logo_path' => $data['logo_path'] ?? null,
            'user_id' => $user->id,
        ]);
    }
}
