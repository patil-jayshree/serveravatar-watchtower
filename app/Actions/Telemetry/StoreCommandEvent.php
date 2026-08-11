<?php

declare(strict_types=1);

namespace App\Actions\Telemetry;

use App\Models\CommandEvent;
use App\Models\Project;
use Carbon\Carbon;

class StoreCommandEvent
{
    public function __construct(
        protected StoreException $storeException,
    ) {}

    /**
     * Store a command execution event from the agent.
     *
     * @param array<string, mixed> $data
     */
    public function execute(Project $project, array $data): CommandEvent
    {
        $commandEvent = CommandEvent::updateOrCreate(
            ['uuid' => $data['command_uuid']],
            [
                'project_id' => $project->id,
                'command_name' => $data['command_name'],
                'status' => $data['status'],
                'exit_code' => $data['exit_code'] ?? null,
                'started_at' => $data['started_at'] ?? null,
                'finished_at' => $data['finished_at'] ?? null,
                'duration_ms' => $data['duration_ms'] ?? null,
                'arguments' => $data['arguments'] ?? [],
                'options' => $data['options'] ?? [],
                'request_id' => $data['request_id'] ?? null,
                'exception_class' => $data['exception_class'] ?? null,
                'exception_message' => $data['exception_message'] ?? null,
                'exception_file' => $data['exception_file'] ?? null,
                'exception_line' => $data['exception_line'] ?? null,
                'stack_trace' => $data['stack_trace'] ?? null,
                'environment' => $data['environment'] ?? 'production',
                'agent_version' => $data['agent_version'] ?? null,
                'laravel_version' => $data['laravel_version'] ?? null,
                'php_version' => $data['php_version'] ?? null,
                'server_name' => $data['server_name'] ?? null,
            ]
        );

        // If command failed with an exception, store it
        if ($data['status'] === 'failed' && ! empty($data['exception_class'])) {
            $this->storeFailedCommandException($project, $commandEvent, $data);
        }

        return $commandEvent;
    }

    /**
     * Store a failed command as an exception occurrence.
     *
     * @param array<string, mixed> $data
     */
    protected function storeFailedCommandException(Project $project, CommandEvent $commandEvent, array $data): void
    {
        if (empty($data['exception_class'])) {
            return;
        }

        $exceptionData = [
            'command_uuid' => $commandEvent->uuid,
            'request_id' => $data['request_id'] ?? null,
            'exception_type' => $data['exception_class'],
            'message' => $data['exception_message'] ?? $data['exception_class'],
            'file' => $data['exception_file'] ?? $commandEvent->exception_file,
            'line' => $data['exception_line'] ?? $commandEvent->exception_line,
            'stack_trace' => $data['stack_trace'] ?? null,
            'status_code' => null,
            'method' => null,
            'path' => null,
            'route_name' => null,
            'controller_action' => $data['command_name'] ?? null,
            'host' => null,
            'user_agent' => null,
            'environment' => $data['environment'] ?? null,
            'laravel_version' => $data['laravel_version'] ?? null,
            'php_version' => $data['php_version'] ?? null,
            'agent_version' => $data['agent_version'] ?? null,
            'occurred_at' => isset($data['finished_at'])
                ? Carbon::createFromTimestamp($data['finished_at'])
                : null,
        ];

        try {
            $this->storeException->execute($project, $exceptionData);
        } catch (\Throwable $e) {
            // Don't fail command event storage if exception storage fails
            report($e);
        }
    }
}
