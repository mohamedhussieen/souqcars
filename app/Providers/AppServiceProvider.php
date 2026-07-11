<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

/** Registers application-level bindings and bootstrapping logic. */
class AppServiceProvider extends ServiceProvider
{
    /** Registers any application services. */
    public function register(): void
    {
        //
    }

    /** Bootstraps any application services. */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers());
    }
}
