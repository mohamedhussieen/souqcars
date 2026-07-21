<?php

namespace App\Providers;

use App\Models\Car;
use App\Observers\CarObserver;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Factory;

/** Registers application-level bindings and bootstrapping logic. */
class AppServiceProvider extends ServiceProvider
{
    /** Registers any application services. */
    public function register(): void
    {
        $this->app->singleton(Messaging::class, function () {
            return (new Factory)
                ->withServiceAccount(config('services.firebase.credentials'))
                ->createMessaging();
        });
    }

    /** Bootstraps any application services. */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers());

        // This is a JSON-only API with no login view; never attempt a redirect on
        // unauthenticated requests (avoids a 500 from the missing 'login' named route
        // when a client omits Accept: application/json).
        Authenticate::redirectUsing(fn () => null);

        Car::observe(CarObserver::class);

        $this->registerRateLimiters();
    }

    /** Registers named rate limiters used by the `throttle:` middleware across mobile and admin route groups. */
    private function registerRateLimiters(): void
    {
        RateLimiter::for('mobile-auth', fn ($request) => Limit::perMinute(10)->by($request->ip()));

        RateLimiter::for('mobile-sensitive', fn ($request) => Limit::perMinute(3)->by($request->ip()));

        RateLimiter::for('mobile-api', function ($request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('admin-api', fn ($request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
    }
}
