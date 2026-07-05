<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates pagination and optional brand-filter query parameters for the admin car model list. */
class AdminCarModelListRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Validates page/per_page bounds and an optional brand_id filter. */
    public function rules(): array
    {
        return [
            'page'     => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'brand_id' => ['sometimes', 'nullable', 'integer', 'exists:brands,id'],
        ];
    }

    /** Returns the per_page value clamped to the allowed maximum of 50. */
    public function perPage(): int
    {
        return min((int) $this->input('per_page', 15), 50);
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
