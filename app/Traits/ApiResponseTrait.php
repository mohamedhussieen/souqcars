<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/** Provides standardized JSON response methods for all API controllers. */
trait ApiResponseTrait
{
    /** Returns a successful non-paginated JSON response. */
    public function success(mixed $data = null, string $message = '', int $code = 200): JsonResponse
    {
        if (empty($message)) {
            $message = __('messages.success');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => null,
            'errors'  => null,
        ], $code);
    }

    /** Returns a successful paginated JSON response with meta block extracted from the paginator. */
    public function successPaginated(LengthAwarePaginator $paginator, string $message = ''): JsonResponse
    {
        if (empty($message)) {
            $message = __('messages.success');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page'  => $paginator->currentPage(),
                'per_page'      => $paginator->perPage(),
                'total'         => $paginator->total(),
                'last_page'     => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
            'errors'  => null,
        ], 200);
    }

    /** Returns a generic error JSON response. */
    public function error(string $message = '', int $code = 400, mixed $errors = null): JsonResponse
    {
        if (empty($message)) {
            $message = __('messages.error');
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'meta'    => null,
            'errors'  => $errors,
        ], $code);
    }

    /** Returns a 404 not found JSON response. */
    public function notFound(string $message = ''): JsonResponse
    {
        if (empty($message)) {
            $message = __('messages.not_found');
        }

        return $this->error($message, 404);
    }

    /** Returns a 401 unauthorized JSON response. */
    public function unauthorized(string $message = ''): JsonResponse
    {
        if (empty($message)) {
            $message = __('messages.unauthorized');
        }

        return $this->error($message, 401);
    }

    /** Returns a 422 validation error JSON response with field-level errors. */
    public function validationError(mixed $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => __('messages.validation_error'),
            'data'    => null,
            'meta'    => null,
            'errors'  => $errors,
        ], 422);
    }
}
