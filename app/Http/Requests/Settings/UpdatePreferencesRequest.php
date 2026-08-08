<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $allowedTimezones = timezone_identifiers_list();
        $allowedLocales = ['en', 'es', 'fr', 'de', 'pt', 'zh', 'ja'];
        $allowedThemes = ['light', 'dark', 'system'];

        return [
            'timezone' => ['sometimes', 'string', 'in:' . implode(',', $allowedTimezones)],
            'locale' => ['sometimes', 'string', 'in:' . implode(',', $allowedLocales)],
            'theme_preference' => ['sometimes', 'string', 'in:' . implode(',', $allowedThemes)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'timezone.in' => 'Invalid timezone selected.',
            'locale.in' => 'Invalid locale selected.',
            'theme_preference.in' => 'Invalid theme preference selected.',
        ];
    }
}
