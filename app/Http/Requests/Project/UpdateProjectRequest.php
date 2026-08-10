<?php

namespace App\Http\Requests\Project;

use App\Enums\Project\ProjectEnvironment;
use App\Enums\Project\ProjectFramework;
use App\Enums\Project\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'framework' => ['sometimes', 'required', 'string', Rule::in(ProjectFramework::values())],
            'environment' => ['sometimes', 'required', 'string', Rule::in(ProjectEnvironment::values())],
            'status' => ['sometimes', 'required', 'string', Rule::in(ProjectStatus::values())],
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
            'name.required' => 'Project name is required.',
            'name.max' => 'Project name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'framework.required' => 'Please select a framework.',
            'framework.in' => 'Selected framework is not valid.',
            'environment.required' => 'Please select an environment.',
            'environment.in' => 'Selected environment is not valid.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Selected status is not valid.',
        ];
    }
}
