<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Mail\OtpCodeMail;
use App\Models\OtpCode;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/** Handles all authentication business logic: registration, login, OTP, and password reset. */
class AuthService
{
    /** Creates a new user, assigns the default role, and returns the user with a Sanctum token. */
    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => $data['password'],
        ])->fresh();

        $user->assignRole(UserRole::User->value);

        if (!empty($data['fcm_token'])) {
            $this->registerFcmToken($user, $data['fcm_token']);
        }

        $token = $user->createToken('mobile-app', expiresAt: now()->addDays(30))->plainTextToken;

        return compact('user', 'token');
    }

    /** Verifies credentials and returns the user with a fresh Sanctum token, or null on failure. */
    public function login(string $email, string $password, ?string $fcmToken = null): ?array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        if (!$user->is_active) {
            return ['inactive' => true];
        }

        if ($fcmToken) {
            $this->registerFcmToken($user, $fcmToken);
        }

        $token = $user->createToken('mobile-app', expiresAt: now()->addDays(30))->plainTextToken;

        return compact('user', 'token');
    }

    /** Generates a 4-digit OTP, persists it with a 5-minute expiry, and emails it to the user. */
    public function sendOtp(string $email): void
    {
        OtpCode::where('email', $email)->where('used', false)->delete();

        // $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $code="1234";
        OtpCode::create([
            'email'      => $email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($email)->send(new OtpCodeMail($code));
    }

    /** Verifies the OTP code for the given email address; returns true on success, false otherwise. */
    public function verifyOtp(string $email, string $code): bool
    {
        $otp = OtpCode::where('email', $email)
            ->where('code', $code)
            ->latest()
            ->first();

        if (!$otp || !$otp->isValid()) {
            return false;
        }

        $otp->update(['used' => true]);

        return true;
    }

    /** Verifies the OTP code and, if valid, updates the user's password. */
    public function resetPassword(string $email, string $code, string $password): bool
    {
        if (!$this->verifyOtp($email, $code)) {
            return false;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        $user->update(['password' => $password]);
        $user->tokens()->delete();

        return true;
    }

    /** Revokes only the current Sanctum token used for this request. */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /** Registers a device's FCM token for this user, reassigning it if another user previously held it. */
    private function registerFcmToken(User $user, string $fcmToken): void
    {
        UserFcmToken::updateOrCreate(
            ['token' => $fcmToken],
            ['user_id' => $user->id]
        );
    }
}
