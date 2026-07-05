<?php

namespace App\Http\Requests\Profile;

use App\Enums\ThemeMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validates notification and theme preference update. */
class UpdatePreferencesRequest extends FormRequest
{
    /** Only authenticated users may update preferences. */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** Validates boolean notification flag and theme against the ThemeMode enum values. */
    public function rules(): array
    {
        return [
            'notification_enabled' => ['required', 'boolean'],
            'theme'                => ['required', 'string', Rule::in(ThemeMode::values())],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
