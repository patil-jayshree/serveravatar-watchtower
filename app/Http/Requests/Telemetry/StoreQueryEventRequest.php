<?php

namespace App\Http\Requests\Telemetry;

use Illuminate\Foundation\Http\FormRequest;

class StoreQueryEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by the agent token middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'request_id' => ['nullable', 'string', 'max:100'],
            'sql' => ['required', 'string', 'max:65535'],
            'bindings' => ['nullable', 'array'],
            'duration_ms' => ['required', 'integer', 'min:0'],
            'connection_name' => ['nullable', 'string', 'max:100'],
            'driver' => ['nullable', 'string', 'max:50'],
            'database_name' => ['nullable', 'string', 'max:255'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'occurred_at' => ['required', 'date'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure duration_ms is an integer
        if ($this->has('duration_ms')) {
            $this->merge([
                'duration_ms' => (int) $this->input('duration_ms'),
            ]);
        }
    }
}
