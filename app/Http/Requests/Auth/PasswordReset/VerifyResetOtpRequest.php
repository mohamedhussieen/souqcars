<?php

namespace App\Http\Requests\Auth\PasswordReset;

use Illuminate\Foundation\Http\FormRequest;

/** Validates the email and 6-digit OTP pair before issuing a reset token. */
class VerifyResetOtpRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'otp'   => ['required', 'string', 'digits:4'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
