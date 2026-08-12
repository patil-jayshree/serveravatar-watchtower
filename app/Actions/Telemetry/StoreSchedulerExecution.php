<?php

declare(strict_types=1);

namespace App\Actions\Telemetry;

use App\Models\Project;
use App\Models\SchedulerExecution;
use App\Models\SchedulerTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreSchedulerExecution
{
    public function __construct(
        protected StoreException $storeException
    ) {}

    /**
     * Store a scheduler execution event from the agent.
     *
     * @param array<string, mixed> $data
     */
    public function execute(Project $project, array $data): SchedulerExecution
    {
        return DB::transaction(function () use ($project, $data) {
            // Find or create the scheduler task
            $task = SchedulerTask::where('project_id', $project->id)
                ->where('task_name', $data['task_name'])
                ->first();

            if (! $task) {
                // Create task on the fly if it doesn't exist
                $task = SchedulerTask::create([
                    'project_id' => $project->id,
                    'task_name' => $data['task_name'],
                    'task_type' => $data['task_type'] ?? 'command',
                    'command_name' => $data['command_name'] ?? null,
                    'job_name' => $data['job_name'] ?? null,
                    'job_uuid' => $data['job_uuid'] ?? null,
                    'expression' => $data['expression'] ?? null,
                    'description' => $data['description'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'environment' => $data['environment'] ?? 'production',
                ]);
            }

            // Calculate delay from expected time
            $delayMs = null;
            if (isset($data['expected_at']) && isset($data['started_at'])) {
                $expectedMs = (int) $data['expected_at'] * 1000;
                $startedMs = (int) $data['started_at'] * 1000;
                $delayMs = max(0, $startedMs - $expectedMs);
            }

            // Create or update the execution
            $execution = SchedulerExecution::updateOrCreate(
                ['uuid' => $data['execution_uuid']],
                [
                    'project_id' => $project->id,
                    'scheduler_task_uuid' => $task->uuid,
                    'status' => $data['status'],
                    'expected_at' => isset($data['expected_at'])
                        ? \Carbon\Carbon::createFromTimestamp($data['expected_at'])
                        : null,
                    'started_at' => isset($data['started_at'])
                        ? \Carbon\Carbon::createFromTimestamp($data['started_at'])
                        : null,
                    'finished_at' => isset($data['finished_at'])
                        ? \Carbon\Carbon::createFromTimestamp($data['finished_at'])
                        : null,
                    'duration_ms' => $data['duration_ms'] ?? null,
                    'delay_ms' => $delayMs,
                    'command_name' => $data['command_name'] ?? null,
                    'command_uuid' => $data['command_uuid'] ?? null,
                    'job_name' => $data['job_name'] ?? null,
                    'job_uuid' => $data['job_uuid'] ?? null,
                    'exception_class' => $data['exception_class'] ?? null,
                    'exception_message' => $data['exception_message'] ?? null,
                    'stack_trace' => $data['stack_trace'] ?? null,
                    'environment' => $data['environment'] ?? 'production',
                    'agent_version' => $data['agent_version'] ?? null,
                    'laravel_version' => $data['laravel_version'] ?? null,
                    'php_version' => $data['php_version'] ?? null,
                    'server_name' => $data['server_name'] ?? null,
                ]
            );

            // If failed with an exception, store it in exception system
            if ($data['status'] === 'failed' && ! empty($data['exception_class'])) {
                $exceptionResult = $this->storeFailedSchedulerException($project, $task, $execution, $data);
                if ($exceptionResult) {
                    $execution->update([
                        'exception_uuid' => $exceptionResult['occurrence_uuid'],
                    ]);
                }
            }

            // Update the task's last run info
            $task->update([
                'last_run_at' => $execution->started_at,
                'last_status' => $data['status'],
                'next_run_at' => isset($data['next_run_at'])
                    ? \Carbon\Carbon::createFromTimestamp($data['next_run_at'])
                    : $task->next_run_at,
            ]);

            return $execution;
        });
    }

    /**
     * Store a failed scheduler execution as an exception occurrence.
     *
     * @param array<string, mixed> $data
     * @return array{group_uuid: string, occurrence_uuid: string}|null
     */
    protected function storeFailedSchedulerException(
        Project $project,
        SchedulerTask $task,
        SchedulerExecution $execution,
        array $data
    ): ?array {
        if (empty($data['exception_class'])) {
            return null;
        }

        $exceptionData = [
            'scheduler_uuid' => $execution->uuid,
            'controller_action' => $data['task_name'] ?? null,
            'exception_type' => $data['exception_class'] ?? 'RuntimeException',
            'message' => $data['exception_message'] ?? $data['exception_class'],
            'file' => $data['exception_file'] ?? 'scheduler',
            'line' => $data['exception_line'] ?? 0,
            'stack_trace' => $data['stack_trace'] ?? null,
            'status_code' => null,
            'method' => null,
            'path' => null,
            'route_name' => null,
            'host' => null,
            'user_agent' => null,
            'environment' => $data['environment'] ?? null,
            'laravel_version' => $data['laravel_version'] ?? null,
            'php_version' => $data['php_version'] ?? null,
            'agent_version' => $data['agent_version'] ?? null,
            'occurred_at' => isset($data['finished_at'])
                ? \Carbon\Carbon::createFromTimestamp($data['finished_at'])
                : null,
        ];

        try {
            return $this->storeException->execute($project, $exceptionData);
        } catch (\Throwable $e) {
            // Don't fail scheduler execution storage if exception storage fails
            report($e);
            return null;
        }
    }
}
