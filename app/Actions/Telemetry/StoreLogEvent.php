<?php

namespace App\Actions\Telemetry;

use App\Models\LogEvent;
use App\Models\Project;

class StoreLogEvent
{
    /**
     * Store a log event from the agent.
     */
    public function execute(array $data): LogEvent
    {
        $validated = $this->validate($data);

        $logEvent = LogEvent::create([
            'uuid' => $validated['uuid'],
            'project_id' => $validated['project_id'],
            'level' => strtoupper($validated['level']),
            'message' => $validated['message'],
            'context' => $validated['context'] ?? null,
            'channel' => $validated['channel'] ?? null,
            'request_id' => $validated['request_id'] ?? null,
            'exception_class' => $validated['exception_class'] ?? null,
            'exception_message' => $validated['exception_message'] ?? null,
            'file' => $validated['file'] ?? null,
            'line' => $validated['line'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'host' => $validated['host'] ?? null,
            'agent_version' => $validated['agent_version'] ?? null,
            'logged_at' => $validated['logged_at'] ?? now(),
        ]);

        return $logEvent;
    }

    /**
     * Validate the incoming log data.
     */
    protected function validate(array $data): array
    {
        $level = strtoupper($data['level'] ?? 'INFO');

        // Validate level is a known log level
        if (!in_array($level, LogEvent::LEVELS, true)) {
            $level = 'INFO';
        }

        return [
            'uuid' => $data['uuid'] ?? (string) \Illuminate\Support\Str::uuid(),
            'project_id' => (int) ($data['project_id'] ?? 0),
            'level' => $level,
            'message' => $data['message'] ?? '',
            'context' => $this->validateContext($data['context'] ?? null),
            'channel' => $this->validateString($data['channel'] ?? null, 100),
            'request_id' => $this->validateString($data['request_id'] ?? null, 100),
            'exception_class' => $this->validateString($data['exception_class'] ?? null, 255),
            'exception_message' => $this->validateString($data['exception_message'] ?? null, 500),
            'file' => $this->validateString($data['file'] ?? null, 500),
            'line' => isset($data['line']) ? max(0, (int) $data['line']) : null,
            'environment' => $this->validateString($data['environment'] ?? null, 50),
            'host' => $this->validateString($data['host'] ?? null, 255),
            'agent_version' => $this->validateString($data['agent_version'] ?? null, 50),
            'logged_at' => $this->parseTimestamp($data['timestamp'] ?? null),
        ];
    }

    /**
     * Validate and sanitize context.
     */
    protected function validateContext(?array $context): ?array
    {
        if (!$context) {
            return null;
        }

        // Ensure context is a flat array (no nested recursion issues)
        $sanitized = $this->sanitizeContextRecursive($context);

        // Limit context size to prevent abuse
        $json = json_encode($sanitized);
        if (strlen($json) > 64000) {
            // Truncate if too large
            return ['_truncated' => true, '_original_size' => strlen($json)];
        }

        return $sanitized;
    }

    /**
     * Recursively sanitize context array.
     */
    protected function sanitizeContextRecursive(mixed $data, int $depth = 0): mixed
    {
        if ($depth > 10) {
            return '[MAX_DEPTH_EXCEEDED]';
        }

        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                // Skip obviously problematic keys
                if (is_string($key) && (str_contains(strtolower($key), 'password') || str_contains(strtolower($key), 'secret'))) {
                    $result[$key] = '[REDACTED]';
                    continue;
                }
                $result[$key] = $this->sanitizeContextRecursive($value, $depth + 1);
            }
            return $result;
        }

        if (is_string($data) && strlen($data) > 10000) {
            return substr($data, 0, 10000) . '...[TRUNCATED]';
        }

        return $data;
    }

    /**
     * Validate and truncate a string field.
     */
    protected function validateString(?string $value, int $maxLength): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim($value);

        if (strlen($value) > $maxLength) {
            return substr($value, 0, $maxLength);
        }

        return $value;
    }

    /**
     * Parse a timestamp from various formats.
     */
    protected function parseTimestamp(?string $timestamp): \DateTimeImmutable
    {
        if (!$timestamp) {
            return now();
        }

        try {
            return new \DateTimeImmutable($timestamp);
        } catch (\Exception) {
            return now();
        }
    }
}
