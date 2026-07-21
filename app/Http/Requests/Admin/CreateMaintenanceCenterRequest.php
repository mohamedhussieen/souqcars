<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates creation of a new maintenance center. */
class CreateMaintenanceCenterRequest extends FormRequest
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

    /** Requires bilingual names and a phone; everything else is optional. */
    public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255'],
            'name_en'    => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:50'],
            'whatsapp'   => ['nullable', 'string', 'max:50'],
            'address_ar' => ['nullable', 'string', 'max:255'],
            'address_en' => ['nullable', 'string', 'max:255'],
            'lat'        => ['nullable', 'numeric', 'between:-90,90'],
            'lng'        => ['nullable', 'numeric', 'between:-180,180'],
            'is_active'  => ['boolean'],
            'logo'       => ['nullable', 'file', 'max:2048', 'mimetypes:image/jpeg,image/png,image/webp'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
