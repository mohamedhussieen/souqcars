<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/** Reads the Accept-Language header and sets the app locale for the request lifecycle. */
class EnsureLocale
{
    /** Sets app locale to 'ar' or 'en' based on Accept-Language header, defaulting to 'ar'. */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language', 'ar');
        $locale = in_array($locale, ['ar', 'en']) ? $locale : 'ar';

        App::setLocale($locale);

        return $next($request);
    }
}
