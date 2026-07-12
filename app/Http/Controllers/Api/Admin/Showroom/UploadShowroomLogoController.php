<?php

namespace App\Http\Controllers\Api\Admin\Showroom;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UploadLogoRequest;
use App\Services\ShowroomService;

/** Uploads (replacing any existing) the showroom logo. */
class UploadShowroomLogoController extends BaseApiController
{
    public function __construct(private readonly ShowroomService $showroomService) {}

    /** Stores the logo and returns its URL. */
    public function __invoke(UploadLogoRequest $request)
    {
        $showroom = $this->showroomService->update(
            $this->showroomService->get(),
            [],
            $request->file('logo')
        );

        return $this->success([
            'logo_url' => $showroom->logo_url,
        ], __('messages.admin.showroom_logo_uploaded'));
    }
}
