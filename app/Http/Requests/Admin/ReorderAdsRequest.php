<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates a bulk ad-reorder request. */
class ReorderAdsRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires a non-empty list of {id, sort_order} pairs referencing existing ads. */
    public function rules(): array
    {
        return [
            'items'              => ['required', 'array', 'min:1'],
            'items.*.id'         => ['required', 'integer', 'exists:ads,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
