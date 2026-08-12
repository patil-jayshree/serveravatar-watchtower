<?php

declare(strict_types=1);

namespace App\Http\Requests\Telemetry;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchedulerTaskRequest extends FormRequest
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
            'task_name' => ['required', 'string', 'max:255'],
            'task_type' => ['nullable', 'string', 'in:command,job,closure,event'],
            'command_name' => ['nullable', 'string', 'max:255'],
            'job_name' => ['nullable', 'string', 'max:255'],
            'job_uuid' => ['nullable', 'string', 'max:100'],
            'expression' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'environment' => ['nullable', 'string', 'max:50'],
            'next_run_at' => ['nullable', 'integer'],
            'last_run_at' => ['nullable', 'integer'],
            'last_status' => ['nullable', 'string', 'in:healthy,running,failed,missed'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'task_name.required' => 'The task name is required.',
        ];
    }
}
