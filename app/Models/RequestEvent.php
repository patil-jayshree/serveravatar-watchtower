<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RequestEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'project_id',
        'request_id',
        'method',
        'path',
        'url',
        'route_name',
        'controller_action',
        'status_code',
        'response_body',
        'error_type',
        'error_message',
        'duration_ms',
        'memory_bytes',
        'host',
        'user_agent',
        'ip',
        'environment',
        'content_type',
        'requested_at',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'duration_ms' => 'integer',
        'memory_bytes' => 'integer',
        'requested_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (RequestEvent $event) {
            if (empty($event->uuid)) {
                $event->uuid = Str::uuid()->toString();
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the related log events for this request.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(LogEvent::class, 'request_id', 'request_id');
    }

    public function isSuccessful(): bool
    {
        return $this->status_code >= 200 && $this->status_code < 400;
    }

    public function isError(): bool
    {
        return $this->status_code >= 400;
    }

    public function statusText(): string
    {
        return match (true) {
            $this->status_code >= 200 && $this->status_code < 300 => 'Success',
            $this->status_code >= 300 && $this->status_code < 400 => 'Redirect',
            $this->status_code >= 400 && $this->status_code < 500 => 'Client Error',
            $this->status_code >= 500 => 'Server Error',
            default => 'Unknown',
        };
    }

    public function hasErrorDetails(): bool
    {
        return $this->isError() && (
            ! empty($this->error_message) ||
            ! empty($this->response_body)
        );
    }

    public function getHumanStatusText(): string
    {
        return match ($this->status_code) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            419 => 'Page Expired',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            429 => 'Too Many Requests',
            495 => 'SSL Certificate Error',
            496 => 'SSL Certificate Required',
            497 => 'HTTP Request Sent to HTTPS Port',
            499 => 'Client Closed Request',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            default => $this->statusText(),
        };
    }

    /**
     * Get the error type label based on status code.
     */
    public function getErrorTypeLabel(): ?string
    {
        if (! $this->isError()) {
            return null;
        }

        // If we have an explicit error_type from the agent, use it
        if (! empty($this->error_type)) {
            return $this->error_type;
        }

        // Otherwise derive from status code
        return match ($this->status_code) {
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            422 => 'Validation Error',
            423 => 'Locked',
            424 => 'Failed Dependency',
            429 => 'Too Many Requests',
            499 => 'Client Closed Request',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            default => 'Error',
        };
    }

    /**
     * Get sanitized error message for display.
     * Removes sensitive data patterns.
     */
    public function getSanitizedErrorMessage(): ?string
    {
        $message = $this->error_message ?? $this->response_body;

        if (empty($message)) {
            return null;
        }

        // Decode JSON if it looks like a JSON response
        if (str_starts_with(trim($message), '{') || str_starts_with(trim($message), '[')) {
            $decoded = json_decode($message, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $message = json_encode($this->sanitizeArray($decoded), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }
        }

        return $message;
    }

    /**
     * Recursively sanitize an array by removing sensitive keys.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function sanitizeArray(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'token',
            'api_key',
            'apiKey',
            'client_secret',
            'clientSecret',
            'access_token',
            'refresh_token',
            'authorization',
            'cookie',
            'session',
            'x-api-token',
            'x-auth-token',
            'x-csrf-token',
            'csrf_token',
            'request_token',
            'secret',
            'private_key',
            'privateKey',
            'credentials',
        ];

        $result = [];
        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string) $key);
            foreach ($sensitiveKeys as $sensitive) {
                if (str_contains($lowerKey, strtolower($sensitive))) {
                    $result[$key] = '[REDACTED]';
                    continue 2;
                }
            }

            if (is_array($value)) {
                $result[$key] = $this->sanitizeArray($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
