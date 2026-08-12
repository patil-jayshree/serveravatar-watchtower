<?php

declare(strict_types=1);

namespace App\Actions\Telemetry;

use App\Models\Project;
use App\Models\SchedulerTask;

class StoreSchedulerTask
{
    /**
     * Store or update a scheduler task from the agent.
     *
     * @param array<string, mixed> $data
     */
    public function execute(Project $project, array $data): SchedulerTask
    {
        $task = SchedulerTask::updateOrCreate(
            [
                'project_id' => $project->id,
                'task_name' => $data['task_name'],
            ],
            [
                'task_type' => $data['task_type'] ?? 'command',
                'command_name' => $data['command_name'] ?? null,
                'job_name' => $data['job_name'] ?? null,
                'job_uuid' => $data['job_uuid'] ?? null,
                'expression' => $data['expression'] ?? null,
                'description' => $data['description'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'environment' => $data['environment'] ?? 'production',
                'next_run_at' => isset($data['next_run_at'])
                    ? \Carbon\Carbon::createFromTimestamp($data['next_run_at'])
                    : null,
                'last_run_at' => isset($data['last_run_at'])
                    ? \Carbon\Carbon::createFromTimestamp($data['last_run_at'])
                    : null,
                'last_status' => $data['last_status'] ?? null,
            ]
        );

        return $task;
    }
}
