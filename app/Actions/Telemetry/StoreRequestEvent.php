<?php

namespace App\Actions\Telemetry;

use App\Models\Project;
use App\Models\RequestEvent;
use Illuminate\Support\Str;

class StoreRequestEvent
{
    public static function execute(Project $project, array $data): RequestEvent
    {
        return RequestEvent::create([
            'uuid' => Str::uuid()->toString(),
            'project_id' => $project->id,
            'request_id' => $data['request_id'],
            'method' => strtoupper($data['method']),
            'path' => $data['path'],
            'url' => $data['url'] ?? null,
            'route_name' => $data['route_name'] ?? null,
            'controller_action' => $data['controller_action'] ?? null,
            'status_code' => (int) $data['status_code'],
            'duration_ms' => (int) $data['duration_ms'],
            'memory_bytes' => isset($data['memory_bytes']) ? (int) $data['memory_bytes'] : null,
            'host' => $data['host'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'ip' => $data['ip'] ?? null,
            'environment' => $data['environment'] ?? 'production',
            'content_type' => $data['content_type'] ?? null,
            'requested_at' => $data['requested_at'],
        ]);
    }
}
