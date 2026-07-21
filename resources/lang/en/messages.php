<?php

return [
    'success'          => 'Operation completed successfully.',
    'error'            => 'An error occurred.',
    'not_found'        => 'Resource not found.',
    'unauthorized'     => 'Unauthorized. Please log in.',
    'validation_error' => 'Validation failed.',
    'server_error'     => 'Something went wrong. Please try again later.',
    'throttled'        => 'Too many requests. Please try again later.',

    'auth' => [
        'registered'          => 'Account created successfully.',
        'login_success'       => 'Logged in successfully.',
        'login_failed'        => 'Invalid credentials.',
        'logout_success'      => 'Logged out successfully.',
        'otp_sent'            => 'OTP code sent successfully.',
        'otp_verified'        => 'OTP verified successfully.',
        'otp_invalid'         => 'Invalid or expired OTP code.',
        'otp_expired'         => 'This OTP code has expired. Please request a new one.',
        'otp_too_many_attempts' => 'Too many failed attempts. Please request a new OTP.',
        'reset_token_invalid' => 'Invalid or expired reset token.',
        'password_reset'      => 'Password reset successfully.',
        'account_inactive'    => 'Your account has been deactivated.',
        'otp_mail_subject'    => 'Your verification code',
        'otp_mail_greeting'   => 'Your verification code is:',
        'otp_mail_expiry'     => 'This code will expire in 5 minutes.',
        'password_reset_otp_mail_subject' => 'Your password reset code',
        'password_reset_otp_mail_expiry'  => 'This code will expire in 10 minutes.',
        'policy_not_accepted' => 'You must accept the application policy before continuing.',
        'otp_throttled'       => 'Please wait :seconds seconds before requesting a new OTP.',
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
        'colors_fetched'  => 'Colors retrieved successfully.',
    ],

    'home' => [
        'fetched' => 'Home data retrieved successfully.',
    ],

    'cars' => [
        'fetched'              => 'Cars retrieved successfully.',
        'detail_fetched'       => 'Car details retrieved successfully.',
        'my_listings_fetched'  => 'Your listings retrieved successfully.',
        'image_limit_exceeded' => 'A car may have at most 10 images.',
    ],

    'favorites' => [
        'fetched' => 'Favorites retrieved successfully.',
        'added'   => 'Car added to favorites.',
        'removed' => 'Car removed from favorites.',
    ],

    'ratings' => [
        'fetched' => 'Ratings retrieved successfully.',
        'saved'   => 'Rating saved successfully.',
    ],

    'showrooms' => [
        'fetched'        => 'Showrooms retrieved successfully.',
        'detail_fetched' => 'Showroom retrieved successfully.',
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

        'cars_fetched'        => 'Cars retrieved successfully.',
        'car_fetched'         => 'Car retrieved successfully.',
        'car_created'         => 'Car created successfully.',
        'car_updated'         => 'Car updated successfully.',
        'car_deleted'         => 'Car deleted successfully.',
        'car_images_uploaded' => 'Car images uploaded successfully.',
        'car_image_deleted'   => 'Car image deleted successfully.',
        'inspection_uploaded' => 'Inspection report uploaded successfully.',
        'car_marked_sold'     => 'Car marked as sold successfully.',
        'car_status_updated'  => 'Car status updated successfully.',

        'showroom_fetched'       => 'Showroom retrieved successfully.',
        'showroom_updated'       => 'Showroom updated successfully.',
        'showroom_logo_uploaded' => 'Showroom logo uploaded successfully.',

        'ads_fetched' => 'Ads retrieved successfully.',
        'ad_created'  => 'Ad created successfully.',
        'ad_updated'  => 'Ad updated successfully.',
        'ad_deleted'  => 'Ad deleted successfully.',

        'colors_fetched' => 'Colors retrieved successfully.',
        'color_created'  => 'Color created successfully.',
        'color_updated'  => 'Color updated successfully.',
        'color_deleted'  => 'Color deleted successfully.',
        'color_in_use'   => 'This color is in use by one or more cars and cannot be deleted.',

        'stats_fetched'     => 'Stats retrieved successfully.',
        'analytics_fetched' => 'Analytics retrieved successfully.',

        'maintenance_centers_fetched' => 'Maintenance centers retrieved successfully.',
        'maintenance_center_fetched'  => 'Maintenance center retrieved successfully.',
        'maintenance_center_created'  => 'Maintenance center created successfully.',
        'maintenance_center_updated'  => 'Maintenance center updated successfully.',
        'maintenance_center_deleted'  => 'Maintenance center deleted successfully.',
        'maintenance_center_logo_uploaded' => 'Maintenance center logo uploaded successfully.',
        'maintenance_center_has_bookings'  => 'This maintenance center has pending or confirmed bookings and cannot be deleted.',

        'maintenance_service_created' => 'Service created successfully.',
        'maintenance_service_updated' => 'Service updated successfully.',
        'maintenance_service_deleted' => 'Service deleted successfully.',
        'maintenance_service_has_bookings' => 'This service has pending or confirmed bookings and cannot be deleted.',

        'bookings_fetched'       => 'Bookings retrieved successfully.',
        'booking_status_updated' => 'Booking status updated successfully.',

        'ad_toggled'    => 'Ad status toggled successfully.',
        'ads_reordered' => 'Ads reordered successfully.',

        'brand_logo_uploaded' => 'Brand logo uploaded successfully.',
        'brand_has_cars'      => 'This brand has cars and cannot be deleted.',
        'car_model_has_cars'  => 'This car model has cars and cannot be deleted.',
        'city_has_cars'       => 'This city has cars and cannot be deleted.',

        'watch_requests_fetched' => 'Watch requests retrieved successfully.',
    ],

    'maintenance' => [
        'centers_fetched' => 'Maintenance centers retrieved successfully.',
        'center_fetched'  => 'Maintenance center retrieved successfully.',
    ],

    'bookings' => [
        'created'   => 'Booking created successfully.',
        'fetched'   => 'Bookings retrieved successfully.',
        'detail_fetched' => 'Booking retrieved successfully.',
        'cancelled' => 'Booking cancelled successfully.',
        'conflict'  => 'You already have a booking at this center on this date.',
        'not_cancellable' => 'Only pending or confirmed bookings can be cancelled.',
        'invalid_transition' => 'This booking status change is not allowed.',
        'forbidden' => 'You are not allowed to access this booking.',
    ],

    'notifications' => [
        'fetched'      => 'Notifications retrieved successfully.',
        'marked_read'  => 'Notification marked as read.',
        'all_marked_read' => 'All notifications marked as read.',
        'unread_count' => 'Unread count retrieved successfully.',
        'forbidden'    => 'You are not allowed to access this notification.',

        'car_match_title'         => 'New match for you',
        'car_match_body'          => 'A new listing matches your interests.',
        'booking_confirmed_title' => 'Booking confirmed',
        'booking_confirmed_body'  => 'Your booking has been confirmed.',
        'booking_created_title'   => 'New booking received',
        'booking_created_body'    => 'A new booking was just created.',
        'booking_cancelled_title' => 'Booking cancelled',
        'booking_cancelled_body'  => 'A booking has been cancelled.',
        'booking_completed_title' => 'Booking completed',
        'booking_completed_body'  => 'Your booking has been marked as completed.',
        'price_drop_title'        => 'Price drop on a favorite',
        'price_drop_body'         => 'A car in your favorites just dropped in price.',
        'listing_approved_title'  => 'Listing approved',
        'listing_approved_body'   => 'Your car listing has been approved.',
        'listing_rejected_title'  => 'Listing rejected',
        'listing_rejected_body'   => 'Your car listing has been rejected.',
        'car_available_title'     => 'A car you were waiting for is available',
        'car_available_body'      => 'A new car matching your watch request is now available.',
    ],

    'watch_requests' => [
        'watched'   => 'You will be notified when this car becomes available.',
        'unwatched' => 'Watch request removed.',
        'fetched'   => 'Watch requests retrieved successfully.',
        'not_sold'  => 'You can only watch a brand/model after this listing has been sold.',
    ],

    'profile_stats' => [
        'fetched' => 'Profile stats retrieved successfully.',
    ],
];
