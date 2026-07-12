<?php

namespace App\Http\Requests\Admin;

use App\Enums\BodyType;
use App\Enums\CarCondition;
use App\Enums\FuelType;
use App\Enums\PaymentType;
use App\Enums\Transmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/** Validates admin creation of a car listing (seller is forced to admin/null by the controller). */
class CreateCarRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Validates all car attributes plus optional gallery images and inspection report. */
    public function rules(): array
    {
        return [
            'brand_id'        => ['required', 'integer', 'exists:brands,id'],
            'car_model_id'    => ['required', 'integer', 'exists:car_models,id'],
            'city_id'         => ['required', 'integer', 'exists:cities,id'],
            'color_id'        => ['nullable', 'integer', 'exists:colors,id'],
            'year'            => ['required', 'integer', 'digits:4'],
            'title_ar'        => ['required', 'string', 'max:255'],
            'title_en'        => ['nullable', 'string', 'max:255'],
            'description_ar'  => ['nullable', 'string'],
            'description_en'  => ['nullable', 'string'],
            'price'           => ['required', 'numeric', 'min:0'],
            'payment_type'    => ['sometimes', new Enum(PaymentType::class)],
            'mileage'         => ['nullable', 'integer', 'min:0'],
            'condition'       => ['sometimes', new Enum(CarCondition::class)],
            'transmission'    => ['required', new Enum(Transmission::class)],
            'fuel_type'       => ['required', new Enum(FuelType::class)],
            'body_type'       => ['required', new Enum(BodyType::class)],
            'owners_count'    => ['nullable', 'integer', 'min:1'],
            'images'          => ['sometimes', 'array', 'max:10'],
            'images.*'        => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
            'inspection_file' => ['sometimes', 'nullable', 'file', 'mimes:jpeg,png,pdf', 'max:10240'],
        ];
    }
}
