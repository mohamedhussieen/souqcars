<?php

namespace App\Http\Requests\Admin;

use App\Enums\AdActionType;
use App\Enums\AdType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/** Validates updates to an ad/banner — same shape as CreateAdRequest, but all fields optional. */
class UpdateAdRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** All fields are optional (partial update semantics). */
    public function rules(): array
    {
        return [
            'title_ar'     => ['sometimes', 'nullable', 'string', 'max:255'],
            'title_en'     => ['sometimes', 'nullable', 'string', 'max:255'],
            'type'         => ['sometimes', new Enum(AdType::class)],
            'action_type'  => ['sometimes', 'nullable', new Enum(AdActionType::class)],
            'action_value' => ['sometimes', 'nullable', 'string', 'max:255'],
            'starts_at'    => ['sometimes', 'nullable', 'date'],
            'ends_at'      => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'    => ['sometimes', 'boolean'],
            'sort_order'   => ['sometimes', 'integer', 'min:0'],
            'image'        => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ];
    }
}
