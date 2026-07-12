<?php

namespace App\Http\Requests\Admin;

use App\Enums\AdActionType;
use App\Enums\AdType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/** Validates creation of a new promotional ad/banner. */
class CreateAdRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Validates optional bilingual titles, ad type, optional navigation target, dates, and image. */
    public function rules(): array
    {
        return [
            'title_ar'     => ['nullable', 'string', 'max:255'],
            'title_en'     => ['nullable', 'string', 'max:255'],
            'type'         => ['required', new Enum(AdType::class)],
            'action_type'  => ['nullable', new Enum(AdActionType::class)],
            'action_value' => ['nullable', 'string', 'max:255'],
            'starts_at'    => ['nullable', 'date'],
            'ends_at'      => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'    => ['sometimes', 'boolean'],
            'sort_order'   => ['sometimes', 'integer', 'min:0'],
            'image'        => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ];
    }
}
