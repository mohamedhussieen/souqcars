<?php

use App\Http\Controllers\Api\Admin\Ads\DeleteAdController;
use App\Http\Controllers\Api\Admin\Ads\ListAdsController;
use App\Http\Controllers\Api\Admin\Ads\ReorderAdsController;
use App\Http\Controllers\Api\Admin\Ads\StoreAdController;
use App\Http\Controllers\Api\Admin\Ads\ToggleAdController;
use App\Http\Controllers\Api\Admin\Ads\UpdateAdController;
use App\Http\Controllers\Api\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Api\Admin\Auth\AdminLogoutController;
use App\Http\Controllers\Api\Admin\Auth\AdminMeController;
use App\Http\Controllers\Api\Admin\Bookings\ListBookingsController as AdminListBookingsController;
use App\Http\Controllers\Api\Admin\Bookings\UpdateBookingStatusController;
use App\Http\Controllers\Api\Admin\Cars\DeleteCarController as AdminDeleteCarController;
use App\Http\Controllers\Api\Admin\Cars\DeleteCarImageController;
use App\Http\Controllers\Api\Admin\Cars\ListCarsController as AdminListCarsController;
use App\Http\Controllers\Api\Admin\Cars\MarkCarSoldController;
use App\Http\Controllers\Api\Admin\Cars\ShowCarController as AdminShowCarController;
use App\Http\Controllers\Api\Admin\Cars\StoreCarController;
use App\Http\Controllers\Api\Admin\Cars\UpdateCarController;
use App\Http\Controllers\Api\Admin\Cars\UpdateCarStatusController;
use App\Http\Controllers\Api\Admin\Cars\UploadCarImagesController;
use App\Http\Controllers\Api\Admin\Cars\UploadInspectionController;
use App\Http\Controllers\Api\Admin\Colors\DeleteColorController;
use App\Http\Controllers\Api\Admin\Colors\ListColorsController;
use App\Http\Controllers\Api\Admin\Colors\StoreColorController;
use App\Http\Controllers\Api\Admin\Colors\UpdateColorController;
use App\Http\Controllers\Api\Admin\Showroom\ShowShowroomController as AdminShowShowroomController;
use App\Http\Controllers\Api\Admin\Showroom\UpdateShowroomController;
use App\Http\Controllers\Api\Admin\Showroom\UploadShowroomLogoController;
use App\Http\Controllers\Api\Admin\Brands\DeleteBrandController;
use App\Http\Controllers\Api\Admin\Brands\ListBrandsController;
use App\Http\Controllers\Api\Admin\Brands\StoreBrandController;
use App\Http\Controllers\Api\Admin\Brands\UpdateBrandController;
use App\Http\Controllers\Api\Admin\Brands\UploadBrandLogoController;
use App\Http\Controllers\Api\Admin\CarModels\DeleteCarModelController;
use App\Http\Controllers\Api\Admin\CarModels\ListCarModelsController;
use App\Http\Controllers\Api\Admin\CarModels\StoreCarModelController;
use App\Http\Controllers\Api\Admin\CarModels\UpdateCarModelController;
use App\Http\Controllers\Api\Admin\Cities\DeleteCityController;
use App\Http\Controllers\Api\Admin\Cities\ListCitiesController;
use App\Http\Controllers\Api\Admin\Cities\StoreCityController;
use App\Http\Controllers\Api\Admin\Cities\UpdateCityController;
use App\Http\Controllers\Api\Admin\Maintenance\DeleteMaintenanceCenterController;
use App\Http\Controllers\Api\Admin\Maintenance\DeleteMaintenanceServiceController;
use App\Http\Controllers\Api\Admin\Maintenance\ListMaintenanceCentersController as AdminListMaintenanceCentersController;
use App\Http\Controllers\Api\Admin\Maintenance\StoreMaintenanceCenterController;
use App\Http\Controllers\Api\Admin\Maintenance\StoreMaintenanceServiceController;
use App\Http\Controllers\Api\Admin\Maintenance\UpdateMaintenanceCenterController;
use App\Http\Controllers\Api\Admin\Maintenance\UpdateMaintenanceServiceController;
use App\Http\Controllers\Api\Admin\Maintenance\UploadMaintenanceCenterLogoController;
use App\Http\Controllers\Api\Admin\Stats\BookingsPerStatusController;
use App\Http\Controllers\Api\Admin\Stats\CarsPerMonthController;
use App\Http\Controllers\Api\Admin\Stats\DashboardStatsController;
use App\Http\Controllers\Api\Admin\Stats\TopBrandsController;
use App\Http\Controllers\Api\Admin\Stats\TopCitiesController;
use App\Http\Controllers\Api\Admin\Users\BanUserController;
use App\Http\Controllers\Api\Admin\Users\DeleteUserController;
use App\Http\Controllers\Api\Admin\Users\ListUsersController;
use App\Http\Controllers\Api\Admin\Users\ShowUserController;
use App\Http\Controllers\Api\Admin\Users\ToggleUserActiveController;
use App\Http\Controllers\Api\Admin\Users\UnbanUserController;
use App\Http\Controllers\Api\Admin\Users\UpdateUserRoleController;
use App\Http\Controllers\Api\Admin\WatchRequests\WatchRequestOverviewController;
use App\Http\Controllers\Api\Core\AppConfigController;
use App\Http\Controllers\Api\Core\PolicyTermsController;
use App\Http\Controllers\Api\Mobile\Auth\LoginController;
use App\Http\Controllers\Api\Mobile\Auth\LogoutController;
use App\Http\Controllers\Api\Mobile\Auth\OtpSendController;
use App\Http\Controllers\Api\Mobile\Auth\OtpVerifyController;
use App\Http\Controllers\Api\Mobile\Auth\PasswordReset\ForgotPasswordController;
use App\Http\Controllers\Api\Mobile\Auth\PasswordReset\ResetPasswordController;
use App\Http\Controllers\Api\Mobile\Auth\PasswordReset\VerifyResetOtpController;
use App\Http\Controllers\Api\Mobile\Auth\RegisterController;
use App\Http\Controllers\Api\Mobile\Bookings\CancelBookingController;
use App\Http\Controllers\Api\Mobile\Bookings\CreateBookingController;
use App\Http\Controllers\Api\Mobile\Bookings\ListBookingsController;
use App\Http\Controllers\Api\Mobile\Bookings\ShowBookingController;
use App\Http\Controllers\Api\Mobile\Cars\ListCarsController;
use App\Http\Controllers\Api\Mobile\Cars\SearchCarsController;
use App\Http\Controllers\Api\Mobile\Cars\ShowCarController;
use App\Http\Controllers\Api\Mobile\Favorites\ListFavoritesController;
use App\Http\Controllers\Api\Mobile\Favorites\ToggleFavoriteController;
use App\Http\Controllers\Api\Mobile\Home\HomeController;
use App\Http\Controllers\Api\Mobile\Lookup\BrandModelsController;
use App\Http\Controllers\Api\Mobile\Lookup\BrandsController;
use App\Http\Controllers\Api\Mobile\Lookup\CitiesController;
use App\Http\Controllers\Api\Mobile\Lookup\ColorsController;
use App\Http\Controllers\Api\Mobile\Maintenance\ListMaintenanceCentersController;
use App\Http\Controllers\Api\Mobile\Maintenance\ShowMaintenanceCenterController;
use App\Http\Controllers\Api\Mobile\MyListings\MyListingsController;
use App\Http\Controllers\Api\Mobile\Notifications\ListNotificationsController;
use App\Http\Controllers\Api\Mobile\Notifications\MarkAllNotificationsReadController;
use App\Http\Controllers\Api\Mobile\Notifications\MarkNotificationReadController;
use App\Http\Controllers\Api\Mobile\Notifications\UnreadNotificationCountController;
use App\Http\Controllers\Api\Mobile\Profile\ProfileStatsController;
use App\Http\Controllers\Api\Mobile\Ratings\ListCarRatingsController;
use App\Http\Controllers\Api\Mobile\Ratings\StoreCarRatingController;
use App\Http\Controllers\Api\Mobile\Showrooms\ListShowroomsController;
use App\Http\Controllers\Api\Mobile\Showrooms\ShowroomCarsController;
use App\Http\Controllers\Api\Mobile\Showrooms\ShowShowroomController;
use App\Http\Controllers\Api\Mobile\Profile\AcceptPolicyController;
use App\Http\Controllers\Api\Mobile\Profile\ChangePasswordController;
use App\Http\Controllers\Api\Mobile\Profile\DeleteAccountController;
use App\Http\Controllers\Api\Mobile\Profile\ShowProfileController;
use App\Http\Controllers\Api\Mobile\Profile\UpdatePreferencesController;
use App\Http\Controllers\Api\Mobile\Profile\UpdateProfileController;
use App\Http\Controllers\Api\Mobile\WatchRequests\ListWatchRequestsController;
use App\Http\Controllers\Api\Mobile\WatchRequests\UnwatchCarController;
use App\Http\Controllers\Api\Mobile\WatchRequests\WatchCarController;
use Illuminate\Support\Facades\Route;

// Core — public, app-level maintenance/upgrade config
Route::prefix('core')->middleware('api')->group(function () {
    Route::get('app-config', AppConfigController::class);
});

// Core — public, locale-aware terms & conditions
Route::prefix('core')->middleware(['api', \App\Http\Middleware\EnsureLocale::class])->group(function () {
    Route::get('terms', PolicyTermsController::class);
});

// Admin dashboard — separate auth/guard scope from the mobile API
Route::prefix('admin')->middleware(['api', 'throttle:admin-api'])->group(function () {

    // Auth — public
    Route::post('auth/login', AdminLoginController::class);

    // Auth — protected (no admin-role gate needed, just a valid admin session)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me',       AdminMeController::class);
        Route::post('auth/logout',  AdminLogoutController::class);
    });

    // Stats & analytics — protected, requires the admin role
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('stats',                        DashboardStatsController::class);
        Route::get('analytics/cars-per-month',      CarsPerMonthController::class);
        Route::get('analytics/bookings-per-status', BookingsPerStatusController::class);
        Route::get('analytics/top-brands',          TopBrandsController::class);
        Route::get('analytics/top-cities',          TopCitiesController::class);
    });

    // Users CRUD — protected, requires the admin role
    Route::prefix('users')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',                 ListUsersController::class);
        Route::get('{user}',            ShowUserController::class);
        Route::put('{user}/toggle-active', ToggleUserActiveController::class);
        Route::put('{user}/ban',        BanUserController::class);
        Route::put('{user}/unban',      UnbanUserController::class);
        Route::put('{user}/role',       UpdateUserRoleController::class);
        Route::delete('{user}',         DeleteUserController::class);
    });

    // Cities CRUD — protected, requires the admin role
    Route::prefix('cities')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',        ListCitiesController::class);
        Route::post('/',       StoreCityController::class);
        Route::put('{city}',   UpdateCityController::class);
        Route::delete('{city}', DeleteCityController::class);
    });

    // Brands CRUD — protected, requires the admin role
    Route::prefix('brands')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',         ListBrandsController::class);
        Route::post('/',        StoreBrandController::class);
        Route::put('{brand}',   UpdateBrandController::class);
        Route::delete('{brand}', DeleteBrandController::class);
        Route::post('{brand}/logo', UploadBrandLogoController::class);
    });

    // Car Models CRUD — protected, requires the admin role
    Route::prefix('car-models')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',            ListCarModelsController::class);
        Route::post('/',           StoreCarModelController::class);
        Route::put('{carModel}',   UpdateCarModelController::class);
        Route::delete('{carModel}', DeleteCarModelController::class);
    });

    // Cars CRUD + media + status workflow — protected, requires the admin role
    Route::prefix('cars')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',                      AdminListCarsController::class);
        Route::post('/',                     StoreCarController::class);
        Route::get('{car}',                  AdminShowCarController::class);
        Route::put('{car}',                  UpdateCarController::class);
        Route::delete('{car}',               AdminDeleteCarController::class);
        Route::post('{car}/images',          UploadCarImagesController::class);
        Route::delete('{car}/images/{mediaId}', DeleteCarImageController::class);
        Route::post('{car}/inspection',      UploadInspectionController::class);
        Route::post('{car}/sold',            MarkCarSoldController::class);
        Route::put('{car}/status',           UpdateCarStatusController::class);
    });

    // Showroom profile (single row in Phase 1) — protected, requires the admin role
    Route::prefix('showroom')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',      AdminShowShowroomController::class);
        Route::put('/',      UpdateShowroomController::class);
        Route::post('logo',  UploadShowroomLogoController::class);
    });

    // Ads CRUD — protected, requires the admin role
    Route::prefix('ads')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',      ListAdsController::class);
        Route::post('/',     StoreAdController::class);
        Route::put('reorder', ReorderAdsController::class);
        Route::put('{ad}',   UpdateAdController::class);
        Route::delete('{ad}', DeleteAdController::class);
        Route::put('{ad}/toggle', ToggleAdController::class);
    });

    // Maintenance centers + services CRUD — protected, requires the admin role
    Route::prefix('maintenance-centers')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',         AdminListMaintenanceCentersController::class);
        Route::post('/',        StoreMaintenanceCenterController::class);
        Route::put('{center}',  UpdateMaintenanceCenterController::class);
        Route::delete('{center}', DeleteMaintenanceCenterController::class);
        Route::post('{center}/logo', UploadMaintenanceCenterLogoController::class);

        Route::post('{center}/services',            StoreMaintenanceServiceController::class);
        Route::put('{center}/services/{service}',    UpdateMaintenanceServiceController::class);
        Route::delete('{center}/services/{service}', DeleteMaintenanceServiceController::class);
    });

    // Bookings management — protected, requires the admin role
    Route::prefix('bookings')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',                AdminListBookingsController::class);
        Route::put('{booking}/status', UpdateBookingStatusController::class);
    });

    // Watch requests overview (read-only) — protected, requires the admin role
    Route::middleware(['auth:sanctum', 'admin'])->get('watch-requests', WatchRequestOverviewController::class);

    // Colors CRUD — protected, requires the admin role
    Route::prefix('colors')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',        ListColorsController::class);
        Route::post('/',       StoreColorController::class);
        Route::put('{color}',  UpdateColorController::class);
        Route::delete('{color}', DeleteColorController::class);
    });
});

Route::prefix('v1/mobile')->middleware(['api', \App\Http\Middleware\EnsureLocale::class, 'auth.optional', 'throttle:mobile-api'])->group(function () {

    // Auth — public endpoints
    Route::prefix('auth')->middleware('throttle:mobile-auth')->group(function () {
        Route::post('register',         RegisterController::class);
        Route::post('login',            LoginController::class);
        Route::post('otp/send',         OtpSendController::class);
        Route::post('otp/verify',       OtpVerifyController::class);

        // Forgot-password flow — separate endpoints (send OTP, verify OTP, reset), throttled to curb OTP brute-forcing
        Route::middleware('throttle:6,1')->group(function () {
            Route::post('forgot-password', ForgotPasswordController::class);
            Route::post('verify-reset-otp', VerifyResetOtpController::class);
        });
        Route::post('reset-password', ResetPasswordController::class);

        // Auth — protected
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', LogoutController::class);
        });
    });

    // Profile — policy acceptance (auth required, but not gated by the policy itself)
    Route::middleware('auth:sanctum')->post('profile/accept-policy', AcceptPolicyController::class);

    // Profile — GET temporarily exempted from policy.accepted gate for testing
    // TODO: re-add 'policy.accepted' to this route once ready
    Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
        Route::get('/', ShowProfileController::class);
    });

    // Profile — protected, requires policy acceptance
    Route::prefix('profile')->middleware(['auth:sanctum', 'policy.accepted'])->group(function () {
        Route::put('/',           UpdateProfileController::class);
        Route::put('password',    ChangePasswordController::class);
        Route::put('preferences', UpdatePreferencesController::class);
        Route::delete('/',        DeleteAccountController::class);
    });

    // Lookup — public
    Route::prefix('lookup')->group(function () {
        Route::get('cities',                CitiesController::class);
        Route::get('brands',                BrandsController::class);
        Route::get('brands/{brand}/models', BrandModelsController::class);
        Route::get('colors',                ColorsController::class);
    });

    // Home & discovery — public (guest allowed; auth token enriches is_favorited)
    Route::get('home',   HomeController::class);
    Route::get('search', SearchCarsController::class);

    // Cars — public browsing
    Route::get('cars',                ListCarsController::class);
    Route::get('cars/{car}',          ShowCarController::class);
    Route::get('cars/{car}/ratings',  ListCarRatingsController::class);

    // Cars — authenticated interactions
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('cars/{car}/ratings', StoreCarRatingController::class);
        Route::get('favorites',           ListFavoritesController::class);
        Route::post('favorites/{car}',    ToggleFavoriteController::class);
        Route::get('my-listings',         MyListingsController::class);

        Route::post('cars/{car}/watch',   WatchCarController::class);
        Route::delete('cars/{car}/watch', UnwatchCarController::class);
        Route::get('watch-requests',      ListWatchRequestsController::class);

        Route::get('profile/stats', ProfileStatsController::class);
    });

    // Showrooms — public
    Route::prefix('showrooms')->group(function () {
        Route::get('/',                 ListShowroomsController::class);
        Route::get('{showroom}',        ShowShowroomController::class);
        Route::get('{showroom}/cars',   ShowroomCarsController::class);
    });

    // Maintenance centers — public browsing
    Route::prefix('maintenance-centers')->group(function () {
        Route::get('/',        ListMaintenanceCentersController::class);
        Route::get('{center}', ShowMaintenanceCenterController::class);
    });

    // Bookings — authenticated
    Route::prefix('bookings')->middleware('auth:sanctum')->group(function () {
        Route::post('/',               CreateBookingController::class);
        Route::get('/',                ListBookingsController::class);
        Route::get('{booking}',        ShowBookingController::class);
        Route::delete('{booking}/cancel', CancelBookingController::class);
    });

    // Notifications — authenticated
    Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
        Route::get('/',              ListNotificationsController::class);
        Route::get('unread-count',   UnreadNotificationCountController::class);
        Route::put('read-all',       MarkAllNotificationsReadController::class);
        Route::put('{notification}/read', MarkNotificationReadController::class);
    });
});
