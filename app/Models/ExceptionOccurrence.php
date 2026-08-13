<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExceptionOccurrence extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'uuid';

    /**
     * The primary key type.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exception_group_uuid',
        'project_id',
        'request_id',
        'job_uuid',
        'command_uuid',
        'scheduler_uuid',
        'message',
        'stack_trace',
        'file',
        'line',
        'exception_class',
        'exception_class_name',
        'function',
        'source_file',
        'source_context',
        'status_code',
        'method',
        'path',
        'route_name',
        'controller_action',
        'host',
        'user_agent',
        'environment',
        'laravel_version',
        'php_version',
        'agent_version',
        'occurred_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'line' => 'integer',
        'status_code' => 'integer',
        'occurred_at' => 'datetime',
    ];

    /**
     * Get the exception class name (short form).
     * e.g., "RuntimeException" instead of "Illuminate\\Database\\Eloquent\\ModelNotFoundException"
     */
    public function getExceptionClassNameAttribute(): string
    {
        if (! $this->exception_class) {
            return 'Unknown';
        }

        return class_basename($this->exception_class);
    }

    /**
     * Get the stack trace as a decoded array.
     *
     * @return array<int, array{file?: string, line?: int, class?: string, type?: string, function?: string, args?: mixed}>
     */
    public function getParsedStackTraceAttribute(): array
    {
        if (empty($this->stack_trace)) {
            return [];
        }

        // Try to decode as JSON first (new format)
        $decoded = json_decode($this->stack_trace, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Fall back to treating as plain text - return as single frame with text
        return [['text' => $this->stack_trace]];
    }

    /**
     * Get the function display string.
     */
    public function getFunctionDisplayAttribute(): string
    {
        if (empty($this->function) && empty($this->exception_class_name)) {
            return '—';
        }

        if (empty($this->function)) {
            return $this->exception_class_name;
        }

        if (empty($this->exception_class_name)) {
            return $this->function;
        }

        // If function already contains the class (e.g., "ClassName::method")
        if (str_contains($this->function, '::') || str_contains($this->function, '->')) {
            return $this->function;
        }

        return $this->exception_class_name . '::' . $this->function;
    }

    /**
     * Get the source file display path (relative).
     */
    public function getSourceFileDisplayAttribute(): ?string
    {
        if (empty($this->source_file)) {
            return null;
        }

        return $this->source_file;
    }

    /**
     * Get the parsed source context as an array.
     *
     * @return array<int, array{line: int, content: string, is_failing: bool}>
     */
    public function getParsedSourceContextAttribute(): ?array
    {
        if (empty($this->source_context)) {
            return null;
        }

        $decoded = json_decode($this->source_context, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return null;
        }

        return $decoded;
    }

    /**
     * Get the exception group that owns this occurrence.
     */
    public function exceptionGroup(): BelongsTo
    {
        return $this->belongsTo(ExceptionGroup::class, 'exception_group_uuid');
    }

    /**
     * Get the related job event if this exception originated from a job.
     */
    public function jobEvent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\JobEvent::class, 'job_uuid', 'uuid');
    }

    /**
     * Get the related command event if this exception originated from an Artisan command.
     */
    public function commandEvent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\CommandEvent::class, 'command_uuid', 'uuid');
    }

    /**
     * Get the related scheduler execution if this exception originated from a scheduled task.
     */
    public function schedulerExecution(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\SchedulerExecution::class, 'scheduler_uuid');
    }

    /**
     * Check if this exception originated from a job.
     */
    public function isFromJob(): bool
    {
        return $this->job_uuid !== null;
    }

    /**
     * Check if this exception originated from a scheduled task.
     */
    public function isFromScheduler(): bool
    {
        return $this->scheduler_uuid !== null;
    }

    /**
     * Check if this exception originated from an Artisan command.
     */
    public function isFromCommand(): bool
    {
        return $this->command_uuid !== null;
    }

    /**
     * Get the source of this exception (job, command, http, or other).
     */
    public function getSourceAttribute(): string
    {
        if ($this->isFromCommand()) {
            return 'command';
        }

        if ($this->isFromJob()) {
            return 'job';
        }

        if ($this->isFromScheduler()) {
            return 'scheduler';
        }

        if ($this->hasRequest()) {
            return 'http';
        }

        return 'other';
    }

    /**
     * Get the display name for the source (command name, job name, scheduler task, or request path).
     */
    public function getSourceDisplayAttribute(): ?string
    {
        if ($this->isFromCommand()) {
            return class_basename($this->commandEvent?->command_name ?? 'UnknownCommand');
        }

        if ($this->isFromJob()) {
            return $this->controller_action ?? class_basename($this->jobEvent?->job_name ?? 'UnknownJob');
        }

        if ($this->isFromScheduler()) {
            return $this->controller_action ?? $this->schedulerExecution?->schedulerTask?->task_name ?? 'UnknownSchedulerTask';
        }

        if ($this->hasRequest()) {
            return ($this->method ? $this->method . ' ' : '') . ($this->path ?? $this->request_id ?? 'Unknown');
        }

        return null;
    }

    /**
     * Get the project that owns this occurrence.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the related request event if available.
     */
    public function requestEvent(): BelongsTo
    {
        return $this->belongsTo(RequestEvent::class, 'request_id', 'request_id');
    }

    /**
     * Get the related request event with fallback matching.
     *
     * This handles cases where the request_id in the exception doesn't
     * exactly match the stored request (e.g., due to duplicate suffix being
     * added to the request_id after the exception was captured).
     *
     * @return RequestEvent|null
     */
    public function getRelatedRequestEvent(): ?RequestEvent
    {
        // Try exact request_id match first
        $event = $this->requestEvent()->first();
        if ($event) {
            return $event;
        }

        // Fallback: match by method + path + timestamp (within 5 seconds)
        if (empty($this->method) || empty($this->path)) {
            return null;
        }

        return RequestEvent::where('project_id', $this->project_id)
            ->where('method', strtoupper($this->method))
            ->where('path', $this->path)
            ->when($this->occurred_at, function ($query) {
                $query->whereBetween('requested_at', [
                    $this->occurred_at->subSeconds(5),
                    $this->occurred_at->addSeconds(5),
                ]);
            })
            ->orderBy('requested_at', 'desc')
            ->first();
    }

    /**
     * Check if this occurrence has an associated request.
     */
    public function hasRequest(): bool
    {
        return $this->request_id !== null;
    }

    /**
     * Get a short preview of the message.
     */
    public function getMessagePreviewAttribute(): string
    {
        $message = $this->message;

        if (strlen($message) > 200) {
            return substr($message, 0, 200) . '...';
        }

        return $message;
    }

    /**
     * Scope: filter by project.
     */
    public function scopeForProject($query, Project $project)
    {
        return $query->where('project_id', $project->id);
    }

    /**
     * Scope: filter by exception group.
     */
    public function scopeForGroup($query, ExceptionGroup $group)
    {
        return $query->where('exception_group_uuid', $group->uuid);
    }
}
