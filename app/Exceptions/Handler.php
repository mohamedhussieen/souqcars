<?php

namespace App\Exceptions;

use App\Exceptions\CarImageLimitExceededException;
use App\Exceptions\PasswordResetException;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Throwable;

/** Global exception handler that normalizes all exceptions into the unified API response shape. */
class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /** Renders any exception as a structured JSON response matching the API response shape. */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }

    /** Maps exception types to their correct API response shape and HTTP status code. */
    private function handleApiException(Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return $this->validationError($e->errors());
        }

        if ($e instanceof PasswordResetException) {
            return $this->error(__($e->translationKey()), $e->status());
        }

        if ($e instanceof CarImageLimitExceededException) {
            return $this->error(__($e->translationKey()), $e->status());
        }

        if ($e instanceof ThrottleRequestsException) {
            return $this->error(__('messages.throttled'), 429);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->notFound(__('messages.not_found'));
        }

        if ($e instanceof AuthenticationException) {
            return $this->unauthorized(__('messages.unauthorized'));
        }

        $message = config('app.debug')
            ? $e->getMessage()
            : __('messages.server_error');

        return $this->error($message, 500);
    }
}
