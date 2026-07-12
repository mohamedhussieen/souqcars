<?php

namespace App\Http\Requests\Mobile;

/** Validates the public search endpoint — identical to CarFilterRequest but requires a search term. */
class CarSearchRequest extends CarFilterRequest
{
    /** Requires 'search' (min 2 chars) in addition to all optional filters. */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => ['required', 'string', 'min:2', 'max:255'],
        ]);
    }
}
