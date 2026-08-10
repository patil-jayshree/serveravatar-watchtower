<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}
