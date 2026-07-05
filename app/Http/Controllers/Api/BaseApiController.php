<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;

/** Base controller that injects the unified API response trait for all API controllers. */
abstract class BaseApiController extends Controller
{
    use ApiResponseTrait;
}
