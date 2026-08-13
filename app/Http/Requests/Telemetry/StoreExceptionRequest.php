<?php

declare(strict_types=1);

namespace App\Http\Requests\Telemetry;

use Illuminate\Foundation\Http\FormRequest;

class StoreExceptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'exception_type' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'file' => ['required', 'string', 'max:500'],
            'line' => ['required', 'integer', 'min:1'],
            'stack_trace' => ['required', 'string'],
            'class' => ['nullable', 'string', 'max:255'],
            'function' => ['nullable', 'string', 'max:255'],
            'source_file' => ['nullable', 'string', 'max:500'],
            'source_context' => ['nullable', 'string', 'max:65535'],
            'request_id' => ['nullable', 'string', 'max:100'],
            'status_code' => ['nullable', 'integer', 'min:100', 'max:599'],
            'method' => ['nullable', 'string', 'max:10'],
            'path' => ['nullable', 'string', 'max:500'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'controller_action' => ['nullable', 'string', 'max:255'],
            'host' => ['nullable', 'string', 'max:255'],
            'user_agent' => ['nullable', 'string', 'max:500'],
            'environment' => ['nullable', 'string', 'max:50'],
            'laravel_version' => ['nullable', 'string', 'max:50'],
            'php_version' => ['nullable', 'string', 'max:50'],
            'agent_version' => ['nullable', 'string', 'max:50'],
            'occurred_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'exception_type.required' => 'Exception type is required.',
            'message.required' => 'Exception message is required.',
            'file.required' => 'File location is required.',
            'line.required' => 'Line number is required.',
            'stack_trace.required' => 'Stack trace is required.',
        ];
    }
}
