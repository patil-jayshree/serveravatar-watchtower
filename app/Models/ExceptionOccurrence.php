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
        'message',
        'stack_trace',
        'file',
        'line',
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
     * Get the exception group that owns this occurrence.
     */
    public function exceptionGroup(): BelongsTo
    {
        return $this->belongsTo(ExceptionGroup::class, 'exception_group_uuid');
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
