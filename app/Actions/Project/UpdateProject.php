<?php

namespace App\Actions\Project;

use App\Models\Project;

class UpdateProject
{
    /**
     * Update an existing project.
     *
     * @param Project $project
     * @param array{name?: string, description?: string, framework?: string, environment?: string, status?: string} $data
     */
    public function execute(Project $project, array $data): Project
    {
        $project->update(array_filter([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'framework' => $data['framework'] ?? null,
            'environment' => $data['environment'] ?? null,
            'status' => $data['status'] ?? null,
        ], fn ($value) => $value !== null));

        return $project->fresh();
    }
}
