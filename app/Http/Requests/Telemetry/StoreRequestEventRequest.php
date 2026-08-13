<?php

namespace App\Http\Requests\Telemetry;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by the agent token middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'request_id' => ['required', 'string', 'max:255'],
            'method' => ['required', 'string', 'max:10', 'in:GET,POST,PUT,PATCH,DELETE,OPTIONS,HEAD'],
            'path' => ['required', 'string', 'max:2048'],
            'url' => ['nullable', 'string', 'max:2048'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'controller_action' => ['nullable', 'string', 'max:255'],
            'status_code' => ['required', 'integer', 'min:100', 'max:599'],
            'response_body' => ['nullable', 'string'],
            'error_type' => ['nullable', 'string', 'max:100'],
            'error_message' => ['nullable', 'string'],
            'duration_ms' => ['required', 'integer', 'min:0'],
            'memory_bytes' => ['nullable', 'integer', 'min:0'],
            'host' => ['nullable', 'string', 'max:255'],
            'user_agent' => ['nullable', 'string', 'max:1024'],
            'ip' => ['nullable', 'string', 'max:45'],
            'environment' => ['nullable', 'string', 'max:50'],
            'content_type' => ['nullable', 'string', 'max:100'],
            'requested_at' => ['required', 'date'],
        ];
    }
}
