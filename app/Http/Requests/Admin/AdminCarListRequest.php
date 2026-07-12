<?php

namespace App\Http\Requests\Admin;

use App\Enums\CarStatus;
use App\Enums\SellerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/** Validates pagination and filter query parameters for the admin car listing. */
class AdminCarListRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Validates optional status/seller_type/brand/city/search filters plus pagination bounds. */
    public function rules(): array
    {
        return [
            'page'        => ['sometimes', 'integer', 'min:1'],
            'per_page'    => ['sometimes', 'integer', 'min:1', 'max:50'],
            'status'      => ['sometimes', 'nullable', new Enum(CarStatus::class)],
            'seller_type' => ['sometimes', 'nullable', new Enum(SellerType::class)],
            'brand_id'    => ['sometimes', 'nullable', 'integer', 'exists:brands,id'],
            'city_id'     => ['sometimes', 'nullable', 'integer', 'exists:cities,id'],
            'search'      => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /** Returns the per_page value clamped to the allowed maximum of 50. */
    public function perPage(): int
    {
        return min((int) $this->input('per_page', 15), 50);
    }

    /** Returns only the filter keys understood by CarFilterService. */
    public function filters(): array
    {
        return $this->only(['status', 'seller_type', 'brand_id', 'city_id', 'search']);
    }
}
