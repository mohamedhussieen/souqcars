<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\PolicyTermService;

/** Returns the app's terms & conditions clauses, localized to the request's Accept-Language. */
class PolicyTermsController extends BaseApiController
{
    public function __construct(private readonly PolicyTermService $policyTermService) {}

    /** Fetches all policy terms ordered and locale-aware via PolicyTermService. */
    public function __invoke()
    {
        $terms = $this->policyTermService->getAll();

        return $this->success($terms, __('messages.core.terms_fetched'));
    }
}
