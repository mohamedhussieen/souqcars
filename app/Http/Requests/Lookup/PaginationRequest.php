<?php

namespace App\Http\Requests\Lookup;

use Illuminate\Foundation\Http\FormRequest;

/** Validates pagination query parameters enforcing a maximum of 50 items per page. */
class PaginationRequest extends FormRequest
{
    /** Pagination is available to all requestors. */
    public function authorize(): bool
    {
        return true;
    }

    /** Validates page and per_page as positive integers within allowed bounds. */
    public function rules(): array
    {
        return [
            'page'     => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
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
