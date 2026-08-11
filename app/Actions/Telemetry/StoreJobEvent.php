<?php

declare(strict_types=1);

namespace App\Actions\Telemetry;

use App\Models\JobEvent;
use App\Models\Project;
use Illuminate\Support\Str;

class StoreJobEvent
{
    public function __construct(
        protected StoreException $storeException,
    ) {}

    /**
     * Store a job event from the agent.
     */
    public function execute(Project $project, array $data): JobEvent
    {
        $eventType = $data['event_type'] ?? 'unknown';

        // Map event type to status
        $status = $this->mapEventTypeToStatus($eventType);

        // Find existing job event or create new one
        // We use job_id + project to track the same job across events
        $jobEvent = $this->findOrCreateJobEvent($project, $data, $status);

        // Update based on event type
        $this->updateJobEvent($jobEvent, $data, $eventType, $status);

        // If job failed, also create an exception occurrence
        if ($eventType === 'failed') {
            $this->storeFailedJobException($project, $jobEvent, $data);
        }

        return $jobEvent;
    }

    /**
     * Store a failed job as an exception occurrence.
     */
    protected function storeFailedJobException(Project $project, JobEvent $jobEvent, array $data): void
    {
        if (empty($data['exception_class'])) {
            return;
        }

        $exceptionData = [
            'request_id' => $data['request_id'] ?? null,
            'job_uuid' => $jobEvent->uuid,
            'exception_type' => $data['exception_class'],
            'message' => $data['exception_message'] ?? $data['exception_class'],
            'file' => $data['exception_file'] ?? $jobEvent->exception_file,
            'line' => $data['exception_line'] ?? $jobEvent->exception_line,
            'stack_trace' => $data['stack_trace'] ?? null,
            'status_code' => null,
            'method' => null,
            'path' => null,
            'route_name' => null,
            'controller_action' => $data['job_name'] ?? null,
            'host' => null,
            'user_agent' => null,
            'environment' => $data['environment'] ?? null,
            'laravel_version' => $data['laravel_version'] ?? null,
            'php_version' => $data['php_version'] ?? null,
            'agent_version' => $data['agent_version'] ?? null,
            'occurred_at' => isset($data['failed_at'])
                ? \Carbon\Carbon::createFromTimestamp($data['failed_at'])
                : null,
        ];

        try {
            $this->storeException->execute($project, $exceptionData);
        } catch (\Throwable $e) {
            // Don't fail job event storage if exception storage fails
            report($e);
        }
    }

    /**
     * Map event type to status.
     */
    protected function mapEventTypeToStatus(string $eventType): string
    {
        return match ($eventType) {
            'queued' => JobEvent::STATUS_QUEUED,
            'started' => JobEvent::STATUS_STARTED,
            'completed' => JobEvent::STATUS_COMPLETED,
            'failed' => JobEvent::STATUS_FAILED,
            default => JobEvent::STATUS_QUEUED,
        };
    }

    /**
     * Find existing job event or create new one.
     */
    protected function findOrCreateJobEvent(Project $project, array $data, string $status): JobEvent
    {
        $jobId = $data['job_id'] ?? null;
        $jobUuid = $data['job_uuid'] ?? null;
        $jobName = $data['job_name'] ?? 'UnknownJob';

        // Try to find existing by job_id + project
        if ($jobId) {
            $existing = JobEvent::where('project_id', $project->id)
                ->where('job_id', $jobId)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        // Try to find by job_uuid
        if ($jobUuid) {
            $existing = JobEvent::where('project_id', $project->id)
                ->where('job_uuid', $jobUuid)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        // Create new
        return JobEvent::create([
            'uuid' => $jobUuid ?? (string) Str::uuid(),
            'project_id' => $project->id,
            'job_id' => $jobId,
            'job_uuid' => $jobUuid,
            'job_name' => $jobName,
            'queue' => $data['queue'] ?? null,
            'connection' => $data['connection'] ?? null,
            'status' => $status,
            'attempts' => (int) ($data['attempts'] ?? 1),
            'request_id' => $data['request_id'] ?? null,
            'environment' => $data['environment'] ?? null,
            'agent_version' => $data['agent_version'] ?? null,
            'laravel_version' => $data['laravel_version'] ?? null,
            'php_version' => $data['php_version'] ?? null,
            'server_name' => $data['server_name'] ?? null,
        ]);
    }

    /**
     * Update job event based on event type.
     */
    protected function updateJobEvent(JobEvent $jobEvent, array $data, string $eventType, string $status): void
    {
        $updateData = [
            'status' => $status,
            'attempts' => max($jobEvent->attempts, (int) ($data['attempts'] ?? 1)),
        ];

        switch ($eventType) {
            case 'queued':
                $updateData['queued_at'] = $data['queued_at'] ?? time();
                break;

            case 'started':
                $updateData['started_at'] = $data['started_at'] ?? time();
                $updateData['attempts'] = (int) ($data['attempts'] ?? 1);
                break;

            case 'completed':
                $updateData['completed_at'] = $data['completed_at'] ?? time();
                $updateData['attempts'] = (int) ($data['attempts'] ?? 1);

                if (isset($data['duration_ms'])) {
                    $updateData['duration_ms'] = $data['duration_ms'];
                } elseif ($jobEvent->started_at && isset($data['completed_at'])) {
                    $updateData['duration_ms'] = ($data['completed_at'] - $jobEvent->started_at) * 1000;
                }
                break;

            case 'failed':
                $updateData['failed_at'] = $data['failed_at'] ?? time();
                $updateData['attempts'] = (int) ($data['attempts'] ?? 1);
                $updateData['exception_class'] = $data['exception_class'] ?? null;
                $updateData['exception_message'] = $this->truncateMessage($data['exception_message'] ?? null);
                $updateData['exception_file'] = $data['exception_file'] ?? null;
                $updateData['exception_line'] = $data['exception_line'] ?? null;
                $updateData['stack_trace'] = $this->truncateStackTrace($data['stack_trace'] ?? null);

                if (isset($data['duration_ms'])) {
                    $updateData['duration_ms'] = $data['duration_ms'];
                } elseif ($jobEvent->started_at && isset($data['failed_at'])) {
                    $updateData['duration_ms'] = ($data['failed_at'] - $jobEvent->started_at) * 1000;
                }
                break;
        }

        $jobEvent->update($updateData);
    }

    /**
     * Truncate exception message to prevent storage issues.
     */
    protected function truncateMessage(?string $message): ?string
    {
        if ($message === null) {
            return null;
        }

        return mb_substr($message, 0, 1000);
    }

    /**
     * Truncate stack trace to prevent storage issues.
     */
    protected function truncateStackTrace(?string $stackTrace): ?string
    {
        if ($stackTrace === null) {
            return null;
        }

        return mb_substr($stackTrace, 0, 51200);
    }
}
