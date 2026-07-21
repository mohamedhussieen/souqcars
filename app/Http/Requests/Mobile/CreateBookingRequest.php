<?php

namespace App\Http\Requests\Mobile;

use App\Enums\SellerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validates a new maintenance booking request. */
class CreateBookingRequest extends FormRequest
{
    /** Authorization is enforced by the auth:sanctum route middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Trims all string inputs before validation. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'notes' => is_string($this->notes) ? trim($this->notes) : $this->notes,
        ]);
    }

    /** Requires a valid center/service pair, an optional owned car, a future date, and a time. */
    public function rules(): array
    {
        return [
            'maintenance_center_id'  => ['required', 'integer', 'exists:maintenance_centers,id'],
            'maintenance_service_id' => [
                'required',
                'integer',
                Rule::exists('maintenance_services', 'id')->where('maintenance_center_id', $this->input('maintenance_center_id')),
            ],
            'car_id' => [
                'nullable',
                'integer',
                Rule::exists('cars', 'id')
                    ->where('seller_type', SellerType::Individual->value)
                    ->where('seller_id', $this->user()?->id),
            ],
            'date'  => ['required', 'date', 'after:today'],
            'time'  => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
