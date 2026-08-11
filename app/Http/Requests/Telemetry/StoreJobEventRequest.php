<?php

declare(strict_types=1);

namespace App\Http\Requests\Telemetry;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the agent token middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', 'in:queued,started,completed,failed'],

            // Job identification
            'job_id' => ['nullable', 'string', 'max:255'],
            'job_uuid' => ['nullable', 'string', 'max:255'],
            'job_name' => ['required', 'string', 'max:255'],
            'queue' => ['nullable', 'string', 'max:255'],
            'connection' => ['nullable', 'string', 'max:255'],

            // Status and attempts
            'attempts' => ['nullable', 'integer', 'min:0', 'max:1000'],

            // Timing
            'queued_at' => ['nullable', 'integer'],
            'started_at' => ['nullable', 'integer'],
            'completed_at' => ['nullable', 'integer'],
            'failed_at' => ['nullable', 'integer'],
            'duration_ms' => ['nullable', 'numeric', 'min:0'],

            // Correlation
            'request_id' => ['nullable', 'string', 'max:255'],
            'trace_id' => ['nullable', 'string', 'max:255'],

            // Failure info
            'exception_class' => ['nullable', 'string', 'max:255'],
            'exception_message' => ['nullable', 'string', 'max:1000'],
            'exception_file' => ['nullable', 'string', 'max:500'],
            'exception_line' => ['nullable', 'integer'],
            'stack_trace' => ['nullable', 'string', 'max:51200'],

            // Metadata
            'environment' => ['nullable', 'string', 'max:50'],
            'agent_version' => ['nullable', 'string', 'max:50'],
            'laravel_version' => ['nullable', 'string', 'max:50'],
            'php_version' => ['nullable', 'string', 'max:50'],
            'server_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'event_type.required' => 'The event type is required.',
            'event_type.in' => 'The event type must be one of: queued, started, completed, failed.',
            'job_name.required' => 'The job name is required.',
        ];
    }
}
