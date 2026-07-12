<?php

namespace App\Http\Requests\Admin;

use App\Enums\BodyType;
use App\Enums\CarCondition;
use App\Enums\FuelType;
use App\Enums\PaymentType;
use App\Enums\Transmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/** Validates admin updates to an existing car listing's attributes. */
class UpdateCarRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Validates all car attributes as optional (partial update semantics). */
    public function rules(): array
    {
        return [
            'brand_id'       => ['sometimes', 'integer', 'exists:brands,id'],
            'car_model_id'   => ['sometimes', 'integer', 'exists:car_models,id'],
            'city_id'        => ['sometimes', 'integer', 'exists:cities,id'],
            'color_id'       => ['sometimes', 'nullable', 'integer', 'exists:colors,id'],
            'year'           => ['sometimes', 'integer', 'digits:4'],
            'title_ar'       => ['sometimes', 'string', 'max:255'],
            'title_en'       => ['sometimes', 'nullable', 'string', 'max:255'],
            'description_ar' => ['sometimes', 'nullable', 'string'],
            'description_en' => ['sometimes', 'nullable', 'string'],
            'price'          => ['sometimes', 'numeric', 'min:0'],
            'payment_type'   => ['sometimes', new Enum(PaymentType::class)],
            'mileage'        => ['sometimes', 'nullable', 'integer', 'min:0'],
            'condition'      => ['sometimes', new Enum(CarCondition::class)],
            'transmission'   => ['sometimes', new Enum(Transmission::class)],
            'fuel_type'      => ['sometimes', new Enum(FuelType::class)],
            'body_type'      => ['sometimes', new Enum(BodyType::class)],
            'owners_count'   => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
