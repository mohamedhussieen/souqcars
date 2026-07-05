<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/** Validates the email, OTP code, new password, and confirmation before resetting. */
class ResetPasswordRequest extends FormRequest
{
    /** Password reset is a public endpoint. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires email, a 4-digit OTP code, and a confirmed password of at least 8 characters. */
    public function rules(): array
    {
        return [
            'email'                 => ['required', 'email'],
            'otp_code'              => ['required', 'string', 'digits:4'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
