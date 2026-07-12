<?php

namespace App\Http\Requests\Admin;

use App\Enums\CarStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/** Validates a car status transition, requiring a rejection reason when rejecting. */
class UpdateCarStatusRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires a valid CarStatus value; rejection_reason is required only when status is 'rejected'. */
    public function rules(): array
    {
        return [
            'status'            => ['required', new Enum(CarStatus::class)],
            'rejection_reason'  => ['required_if:status,rejected', 'nullable', 'string', 'max:1000'],
        ];
    }
}
