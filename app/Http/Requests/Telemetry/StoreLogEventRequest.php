<?php

namespace App\Http\Requests\Telemetry;

use App\Models\LogEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLogEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the controller using AgentToken
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string', 'uuid'],
            'level' => ['required', 'string', Rule::in(LogEvent::LEVELS)],
            'message' => ['required', 'string', 'max:10000'],
            'context' => ['nullable', 'array'],
            'channel' => ['nullable', 'string', 'max:100'],
            'request_id' => ['nullable', 'string', 'max:100'],
            'exception_class' => ['nullable', 'string', 'max:255'],
            'exception_message' => ['nullable', 'string', 'max:500'],
            'file' => ['nullable', 'string', 'max:500'],
            'line' => ['nullable', 'integer', 'min:0'],
            'environment' => ['nullable', 'string', 'max:50'],
            'host' => ['nullable', 'string', 'max:255'],
            'agent_version' => ['nullable', 'string', 'max:50'],
            'timestamp' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'uuid.required' => 'Log UUID is required.',
            'uuid.uuid' => 'Log UUID must be a valid UUID.',
            'level.required' => 'Log level is required.',
            'level.in' => 'Invalid log level.',
            'message.required' => 'Log message is required.',
            'message.max' => 'Log message exceeds maximum length.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('level')) {
            $this->merge([
                'level' => strtoupper($this->input('level')),
            ]);
        }
    }
}
