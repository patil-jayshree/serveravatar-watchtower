<?php

namespace App\Actions\Project;

use App\Models\Organization;
use App\Models\Project;

class CreateProject
{
    /**
     * Create a new project.
     *
     * @param Organization $organization
     * @param array{name: string, description?: string, framework: string, environment: string, status?: string} $data
     */
    public function execute(Organization $organization, array $data): Project
    {
        return Project::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'framework' => $data['framework'],
            'environment' => $data['environment'],
            'status' => $data['status'] ?? 'active',
            'organization_id' => $organization->id,
        ]);
    }
}
