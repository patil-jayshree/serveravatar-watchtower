<?php

declare(strict_types=1);

namespace App\Actions\Telemetry;

use App\Models\ExceptionGroup;
use App\Models\ExceptionOccurrence;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreException
{
    /**
     * Store an exception from the agent.
     *
     * @return array{group_uuid: string, occurrence_uuid: string}
     */
    public function execute(Project $project, array $data): array
    {
        return DB::transaction(function () use ($project, $data) {
            // Generate group signature from exception type and location
            $groupSignature = $this->generateGroupSignature(
                $data['exception_type'],
                $data['file'],
                $data['line']
            );

            // Normalize message for display (truncate if too long)
            $normalizedMessage = $this->normalizeMessage($data['message']);

            // Find or create the exception group
            $group = $this->findOrCreateGroup(
                $project,
                $data['exception_type'],
                $groupSignature,
                $normalizedMessage,
                $data['file'],
                $data['line'],
                $data
            );

            // If group was resolved, reopen it
            $group->reopenIfResolved();

            // Create the occurrence
            $occurrence = $this->createOccurrence($project, $group, $data);

            // Update group stats
            $occurredAt = isset($data['occurred_at'])
                ? \Carbon\Carbon::parse($data['occurred_at'])
                : null;
            $group->recordOccurrence($occurredAt);

            return [
                'group_uuid' => $group->uuid,
                'occurrence_uuid' => $occurrence->uuid,
            ];
        });
    }

    /**
     * Generate a stable group signature for the exception.
     */
    protected function generateGroupSignature(string $exceptionType, string $file, int $line): string
    {
        // Use exception type + file path (without full system path) + approximate line
        $fileParts = array_slice(explode('/', $file), -3);
        $normalizedFile = implode('/', $fileParts);

        // Round line number to nearest 5 for grouping similar exceptions
        $approxLine = (int) (floor($line / 5) * 5);

        return Str::slug("{$exceptionType} {$normalizedFile} {$approxLine}");
    }

    /**
     * Normalize the exception message for storage.
     */
    protected function normalizeMessage(string $message): string
    {
        // Truncate long messages
        if (strlen($message) > 500) {
            return substr($message, 0, 500);
        }

        return $message;
    }

    /**
     * Find existing group or create a new one.
     */
    protected function findOrCreateGroup(
        Project $project,
        string $exceptionType,
        string $groupSignature,
        string $normalizedMessage,
        string $file,
        int $line,
        array $data
    ): ExceptionGroup {
        $group = ExceptionGroup::where('project_id', $project->id)
            ->where('group_signature', $groupSignature)
            ->first();

        if ($group) {
            return $group;
        }

        $occurredAt = $data['occurred_at'] ?? now();

        return ExceptionGroup::create([
            'project_id' => $project->id,
            'group_signature' => $groupSignature,
            'exception_type' => $exceptionType,
            'normalized_message' => $normalizedMessage,
            'file' => $file,
            'line' => $line,
            'first_seen_at' => $occurredAt,
            'last_seen_at' => $occurredAt,
            'occurrence_count' => 0,
            'status' => ExceptionGroup::STATUS_OPEN,
        ]);
    }

    /**
     * Create a new exception occurrence.
     */
    protected function createOccurrence(
        Project $project,
        ExceptionGroup $group,
        array $data
    ): ExceptionOccurrence {
        return ExceptionOccurrence::create([
            'exception_group_uuid' => $group->uuid,
            'project_id' => $project->id,
            'request_id' => $data['request_id'] ?? null,
            'job_uuid' => $data['job_uuid'] ?? null,
            'message' => $data['message'],
            'stack_trace' => $this->sanitizeStackTrace($data['stack_trace']),
            'file' => $data['file'],
            'line' => $data['line'],
            'status_code' => $data['status_code'] ?? null,
            'method' => $data['method'] ?? null,
            'path' => $data['path'] ?? null,
            'route_name' => $data['route_name'] ?? null,
            'controller_action' => $data['controller_action'] ?? null,
            'host' => $data['host'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'environment' => $data['environment'] ?? 'production',
            'laravel_version' => $data['laravel_version'] ?? null,
            'php_version' => $data['php_version'] ?? null,
            'agent_version' => $data['agent_version'] ?? null,
            'occurred_at' => $data['occurred_at'] ?? now(),
        ]);
    }

    /**
     * Sanitize stack trace to remove sensitive information.
     */
    protected function sanitizeStackTrace(string $stackTrace): string
    {
        // Remove potential sensitive data patterns
        // Use # as delimiter to avoid conflicts with stack trace paths
        $patterns = [
            '#password["\']?\s*[:=]\s*["\'][^"\']*["\']#i',
            '#secret["\']?\s*[:=]\s*["\'][^"\']*["\']#i',
            '#token["\']?\s*[:=]\s*["\'][^"\']*["\']#i',
            '#api[_-]?key["\']?\s*[:=]\s*["\'][^"\']*["\']#i',
            '#authorization["\']?\s*[:=]\s*["\'][^"\']*["\']#i',
            '#bearer\s+[a-zA-Z0-9\-_.~+/]+#i',
        ];

        $sanitized = $stackTrace;
        foreach ($patterns as $pattern) {
            $sanitized = preg_replace($pattern, '[REDACTED]', $sanitized);
        }

        return $sanitized;
    }
}
