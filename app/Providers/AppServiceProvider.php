<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
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
    }
}
