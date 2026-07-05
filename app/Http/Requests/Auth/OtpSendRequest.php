<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/** Validates the email address before sending an OTP code. */
class OtpSendRequest extends FormRequest
{
    /** OTP sending is a public endpoint. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires a valid email address. */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
