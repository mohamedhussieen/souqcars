<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates creation of a new maintenance service under a center. */
class CreateMaintenanceServiceRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Trims all string inputs before validation. */
    protected function prepareForValidation(): void
    {
        foreach (['name_ar', 'name_en', 'description_ar', 'description_en'] as $field) {
            if (is_string($this->{$field})) {
                $this->merge([$field => trim($this->{$field})]);
            }
        }
    }

    /** Requires bilingual names and a non-negative price. */
    public function rules(): array
    {
        return [
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price'          => ['required', 'numeric', 'min:0'],
            'sort_order'     => ['integer', 'min:0'],
            'is_active'      => ['boolean'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
