<?php

namespace App\Http\Controllers\Api\Core;

use App\Enums\Platform;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Core\AppConfigRequest;
use App\Services\CoreConfigService;
use Illuminate\Support\Facades\App;

/** Returns maintenance and upgrade status for the requesting app platform and version. */
class AppConfigController extends BaseApiController
{
    public function __construct(private readonly CoreConfigService $coreConfigService) {}

    /** Resolves the app config for the given platform/version, applying the requested locale. */
    public function __invoke(AppConfigRequest $request)
    {
        App::setLocale($request->input('language', 'ar'));

        $config = $this->coreConfigService->getConfig(
            Platform::from($request->input('platform')),
            $request->input('version')
        );

        return $this->success($config, __('messages.core.app_config_fetched'));
    }
}
