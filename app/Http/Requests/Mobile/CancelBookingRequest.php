<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

/** Validates an optional cancellation reason when cancelling a booking. */
class CancelBookingRequest extends FormRequest
{
    /** Ownership/admin authorization is enforced inside BookingService::cancel(). */
    public function authorize(): bool
    {
        return true;
    }

    /** Trims the cancellation reason before validation. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cancellation_reason' => is_string($this->cancellation_reason) ? trim($this->cancellation_reason) : $this->cancellation_reason,
        ]);
    }

    /** Allows an optional, short cancellation reason. */
    public function rules(): array
    {
        return [
            'cancellation_reason' => ['nullable', 'string', 'max:300'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
