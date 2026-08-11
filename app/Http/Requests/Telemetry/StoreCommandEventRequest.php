<?php

declare(strict_types=1);

namespace App\Http\Requests\Telemetry;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommandEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the agent token
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Command identification
            'command_uuid' => ['required', 'string', 'max:100'],
            'command_name' => ['required', 'string', 'max:255'],

            // Status
            'status' => ['required', 'string', 'in:started,completed,failed'],

            // Exit code (null for started status)
            'exit_code' => ['nullable', 'integer', 'min:-128', 'max:127'],

            // Timing
            'started_at' => ['nullable', 'integer'],
            'finished_at' => ['nullable', 'integer'],
            'duration_ms' => ['nullable', 'numeric', 'min:0'],

            // Arguments and options (JSON arrays, sanitized)
            'arguments' => ['nullable', 'array'],
            'options' => ['nullable', 'array'],

            // Correlation
            'request_id' => ['nullable', 'string', 'max:255'],

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
            'command_uuid.required' => 'The command UUID is required.',
            'command_name.required' => 'The command name is required.',
            'status.required' => 'The command status is required.',
            'status.in' => 'The status must be one of: started, completed, failed.',
        ];
    }
}
