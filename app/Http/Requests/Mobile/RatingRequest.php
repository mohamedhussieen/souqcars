<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

/** Validates a rating submission for a car. */
class RatingRequest extends FormRequest
{
    /** Authorization is enforced by the auth:sanctum route middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires an integer rating between 1 and 5, and an optional short comment. */
    public function rules(): array
    {
        return [
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }
}
