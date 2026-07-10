<?php

use App\Http\Controllers\Api\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Api\Admin\Auth\AdminLogoutController;
use App\Http\Controllers\Api\Admin\Auth\AdminMeController;
use App\Http\Controllers\Api\Admin\Brands\DeleteBrandController;
use App\Http\Controllers\Api\Admin\Brands\ListBrandsController;
use App\Http\Controllers\Api\Admin\Brands\StoreBrandController;
use App\Http\Controllers\Api\Admin\Brands\UpdateBrandController;
use App\Http\Controllers\Api\Admin\CarModels\DeleteCarModelController;
use App\Http\Controllers\Api\Admin\CarModels\ListCarModelsController;
use App\Http\Controllers\Api\Admin\CarModels\StoreCarModelController;
use App\Http\Controllers\Api\Admin\CarModels\UpdateCarModelController;
use App\Http\Controllers\Api\Admin\Cities\DeleteCityController;
use App\Http\Controllers\Api\Admin\Cities\ListCitiesController;
use App\Http\Controllers\Api\Admin\Cities\StoreCityController;
use App\Http\Controllers\Api\Admin\Cities\UpdateCityController;
use App\Http\Controllers\Api\Admin\Users\DeleteUserController;
use App\Http\Controllers\Api\Admin\Users\ListUsersController;
use App\Http\Controllers\Api\Admin\Users\ShowUserController;
use App\Http\Controllers\Api\Admin\Users\ToggleUserActiveController;
use App\Http\Controllers\Api\Admin\Users\UpdateUserRoleController;
use App\Http\Controllers\Api\Core\AppConfigController;
use App\Http\Controllers\Api\Core\PolicyTermsController;
use App\Http\Controllers\Api\Mobile\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Mobile\Auth\LoginController;
use App\Http\Controllers\Api\Mobile\Auth\LogoutController;
use App\Http\Controllers\Api\Mobile\Auth\OtpSendController;
use App\Http\Controllers\Api\Mobile\Auth\OtpVerifyController;
use App\Http\Controllers\Api\Mobile\Auth\RegisterController;
use App\Http\Controllers\Api\Mobile\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Mobile\Lookup\BrandModelsController;
use App\Http\Controllers\Api\Mobile\Lookup\BrandsController;
use App\Http\Controllers\Api\Mobile\Lookup\CitiesController;
use App\Http\Controllers\Api\Mobile\Profile\AcceptPolicyController;
use App\Http\Controllers\Api\Mobile\Profile\ChangePasswordController;
use App\Http\Controllers\Api\Mobile\Profile\DeleteAccountController;
use App\Http\Controllers\Api\Mobile\Profile\ShowProfileController;
use App\Http\Controllers\Api\Mobile\Profile\UpdatePreferencesController;
use App\Http\Controllers\Api\Mobile\Profile\UpdateProfileController;
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
Route::prefix('admin')->middleware('api')->group(function () {

    // Auth — public
    Route::post('auth/login', AdminLoginController::class);

    // Auth — protected (no admin-role gate needed, just a valid admin session)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me',       AdminMeController::class);
        Route::post('auth/logout',  AdminLogoutController::class);
    });

    // Users CRUD — protected, requires the admin role
    Route::prefix('users')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',                 ListUsersController::class);
        Route::get('{user}',            ShowUserController::class);
        Route::put('{user}/toggle-active', ToggleUserActiveController::class);
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
    });

    // Car Models CRUD — protected, requires the admin role
    Route::prefix('car-models')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/',            ListCarModelsController::class);
        Route::post('/',           StoreCarModelController::class);
        Route::put('{carModel}',   UpdateCarModelController::class);
        Route::delete('{carModel}', DeleteCarModelController::class);
    });
});

Route::prefix('v1/mobile')->middleware(['api', \App\Http\Middleware\EnsureLocale::class])->group(function () {

    // Auth — public endpoints
    Route::prefix('auth')->group(function () {
        Route::post('register',         RegisterController::class);
        Route::post('login',            LoginController::class);
        Route::post('otp/send',         OtpSendController::class);
        Route::post('otp/verify',       OtpVerifyController::class);
        Route::post('forgot-password',  ForgotPasswordController::class);
        Route::post('reset-password',   ResetPasswordController::class);

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
    });
});
