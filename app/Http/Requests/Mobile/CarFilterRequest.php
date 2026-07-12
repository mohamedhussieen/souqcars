<?php

namespace App\Http\Requests\Mobile;

use App\Enums\BodyType;
use App\Enums\CarCondition;
use App\Enums\FuelType;
use App\Enums\PaymentType;
use App\Enums\SellerType;
use App\Enums\SortBy;
use App\Enums\Transmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/** Validates pagination and filter/sort query parameters for the public car listing/search endpoints. */
class CarFilterRequest extends FormRequest
{
    /** Filtering/browsing cars is available to all requestors. */
    public function authorize(): bool
    {
        return true;
    }

    /** Validates all optional filters plus pagination bounds. */
    public function rules(): array
    {
        return [
            'page'           => ['sometimes', 'integer', 'min:1'],
            'per_page'       => ['sometimes', 'integer', 'min:1', 'max:50'],
            'brand_id'       => ['sometimes', 'nullable', 'integer', 'exists:brands,id'],
            'car_model_id'   => ['sometimes', 'nullable', 'integer', 'exists:car_models,id'],
            'city_id'        => ['sometimes', 'nullable', 'integer', 'exists:cities,id'],
            'color_id'       => ['sometimes', 'nullable', 'integer', 'exists:colors,id'],
            'year_from'      => ['sometimes', 'nullable', 'integer'],
            'year_to'        => ['sometimes', 'nullable', 'integer'],
            'price_min'      => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_max'      => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'mileage_max'    => ['sometimes', 'nullable', 'integer', 'min:0'],
            'condition'      => ['sometimes', 'nullable', new Enum(CarCondition::class)],
            'transmission'   => ['sometimes', 'nullable', new Enum(Transmission::class)],
            'fuel_type'      => ['sometimes', 'nullable', new Enum(FuelType::class)],
            'body_type'      => ['sometimes', 'nullable', new Enum(BodyType::class)],
            'payment_type'   => ['sometimes', 'nullable', new Enum(PaymentType::class)],
            'has_inspection' => ['sometimes', 'nullable', 'boolean'],
            'seller_type'    => ['sometimes', 'nullable', new Enum(SellerType::class)],
            'search'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort_by'        => ['sometimes', 'nullable', new Enum(SortBy::class)],
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
        return $this->only([
            'brand_id', 'car_model_id', 'city_id', 'color_id',
            'year_from', 'year_to', 'price_min', 'price_max', 'mileage_max',
            'condition', 'transmission', 'fuel_type', 'body_type', 'payment_type',
            'has_inspection', 'seller_type', 'search', 'sort_by',
        ]);
    }
}
