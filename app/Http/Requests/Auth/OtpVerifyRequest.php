<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/** Validates the email and OTP code pair before verification. */
class OtpVerifyRequest extends FormRequest
{
    /** OTP verification is a public endpoint. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires email and a 4-digit numeric OTP code. */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'otp_code' => ['required', 'string', 'digits:4'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
