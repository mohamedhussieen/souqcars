<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates partial admin updates to a maintenance center. */
class UpdateMaintenanceCenterRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Trims all string inputs before validation. */
    protected function prepareForValidation(): void
    {
        foreach (['name_ar', 'name_en', 'phone', 'whatsapp', 'address_ar', 'address_en'] as $field) {
            if (is_string($this->{$field})) {
                $this->merge([$field => trim($this->{$field})]);
            }
        }
    }

    /** All fields are optional (partial update semantics). */
    public function rules(): array
    {
        return [
            'name_ar'    => ['sometimes', 'string', 'max:255'],
            'name_en'    => ['sometimes', 'string', 'max:255'],
            'phone'      => ['sometimes', 'string', 'max:50'],
            'whatsapp'   => ['sometimes', 'nullable', 'string', 'max:50'],
            'address_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'lat'        => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'lng'        => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'is_active'  => ['sometimes', 'boolean'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
