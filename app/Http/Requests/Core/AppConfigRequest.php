<?php

namespace App\Http\Requests\Core;

use App\Enums\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/** Validates the platform, client version, and optional language for app-config lookup. */
class AppConfigRequest extends FormRequest
{
    /** App config is a public endpoint. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires a known platform and a version string; language defaults to Arabic. */
    public function rules(): array
    {
        return [
            'platform' => ['required', new Enum(Platform::class)],
            'version'  => ['required', 'string'],
            'language' => ['sometimes', 'string', 'in:ar,en'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
