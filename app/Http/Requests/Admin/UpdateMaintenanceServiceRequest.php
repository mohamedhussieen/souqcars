<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates partial admin updates to a maintenance service. */
class UpdateMaintenanceServiceRequest extends FormRequest
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

    /** All fields are optional (partial update semantics). */
    public function rules(): array
    {
        return [
            'name_ar'        => ['sometimes', 'string', 'max:255'],
            'name_en'        => ['sometimes', 'string', 'max:255'],
            'description_ar' => ['sometimes', 'nullable', 'string'],
            'description_en' => ['sometimes', 'nullable', 'string'],
            'price'          => ['sometimes', 'numeric', 'min:0'],
            'sort_order'     => ['sometimes', 'integer', 'min:0'],
            'is_active'      => ['sometimes', 'boolean'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
