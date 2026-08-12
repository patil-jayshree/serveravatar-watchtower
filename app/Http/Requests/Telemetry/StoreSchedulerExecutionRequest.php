<?php

declare(strict_types=1);

namespace App\Http\Requests\Telemetry;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchedulerExecutionRequest extends FormRequest
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
            // Execution identification
            'execution_uuid' => ['required', 'string', 'max:100'],
            'task_name' => ['required', 'string', 'max:255'],

            // Status
            'status' => ['required', 'string', 'in:started,completed,failed,missed'],

            // Timing
            'expected_at' => ['nullable', 'integer'],
            'started_at' => ['nullable', 'integer'],
            'finished_at' => ['nullable', 'integer'],
            'duration_ms' => ['nullable', 'numeric', 'min:0'],
            'next_run_at' => ['nullable', 'integer'],

            // Task info
            'task_type' => ['nullable', 'string', 'in:command,job,closure,event'],
            'command_name' => ['nullable', 'string', 'max:255'],
            'command_uuid' => ['nullable', 'string', 'max:100'],
            'job_name' => ['nullable', 'string', 'max:255'],
            'job_uuid' => ['nullable', 'string', 'max:100'],
            'expression' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'timezone' => ['nullable', 'string', 'max:100'],

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
            'execution_uuid.required' => 'The execution UUID is required.',
            'task_name.required' => 'The task name is required.',
            'status.required' => 'The execution status is required.',
            'status.in' => 'The status must be one of: started, completed, failed, missed.',
        ];
    }
}
