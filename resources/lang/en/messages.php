<?php

return [
    'success'          => 'Operation completed successfully.',
    'error'            => 'An error occurred.',
    'not_found'        => 'Resource not found.',
    'unauthorized'     => 'Unauthorized. Please log in.',
    'validation_error' => 'Validation failed.',
    'server_error'     => 'Something went wrong. Please try again later.',

    'auth' => [
        'registered'          => 'Account created successfully.',
        'login_success'       => 'Logged in successfully.',
        'login_failed'        => 'Invalid credentials.',
        'logout_success'      => 'Logged out successfully.',
        'otp_sent'            => 'OTP code sent successfully.',
        'otp_verified'        => 'OTP verified successfully.',
        'otp_invalid'         => 'Invalid or expired OTP code.',
        'password_reset'      => 'Password reset successfully.',
        'account_inactive'    => 'Your account has been deactivated.',
        'otp_mail_subject'    => 'Your verification code',
        'otp_mail_greeting'   => 'Your verification code is:',
        'otp_mail_expiry'     => 'This code will expire in 5 minutes.',
        'policy_not_accepted' => 'You must accept the application policy before continuing.',
    ],

    'profile' => [
        'fetched'              => 'Profile retrieved successfully.',
        'updated'              => 'Profile updated successfully.',
        'password_changed'     => 'Password changed successfully.',
        'preferences_updated'  => 'Preferences updated successfully.',
        'deleted'              => 'Account deleted successfully.',
        'wrong_password'       => 'Current password is incorrect.',
        'policy_accepted'      => 'Policy accepted successfully.',
    ],

    'lookup' => [
        'cities_fetched'  => 'Cities retrieved successfully.',
        'brands_fetched'  => 'Brands retrieved successfully.',
        'models_fetched'  => 'Car models retrieved successfully.',
    ],

    'core' => [
        'app_config_fetched' => 'App config retrieved successfully.',
        'terms_fetched'      => 'Terms & conditions retrieved successfully.',
    ],

    'admin' => [
        'forbidden'            => 'This action is restricted to administrators.',
        'users_fetched'        => 'Users retrieved successfully.',
        'user_fetched'         => 'User retrieved successfully.',
        'user_status_updated'  => 'User status updated successfully.',
        'user_role_updated'    => 'User role updated successfully.',
        'user_deleted'         => 'User deleted successfully.',

        'cities_fetched' => 'Cities retrieved successfully.',
        'city_created'   => 'City created successfully.',
        'city_updated'   => 'City updated successfully.',
        'city_deleted'   => 'City deleted successfully.',

        'brands_fetched' => 'Brands retrieved successfully.',
        'brand_created'  => 'Brand created successfully.',
        'brand_updated'  => 'Brand updated successfully.',
        'brand_deleted'  => 'Brand deleted successfully.',

        'car_models_fetched' => 'Car models retrieved successfully.',
        'car_model_created'  => 'Car model created successfully.',
        'car_model_updated'  => 'Car model updated successfully.',
        'car_model_deleted'  => 'Car model deleted successfully.',
    ],
];
