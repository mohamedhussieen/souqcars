<?php

namespace App\Services;

use App\Http\Resources\PolicyTermResource;
use App\Models\PolicyTerm;
use Illuminate\Support\Collection;

/** Handles retrieval of the app's terms & conditions clauses. */
class PolicyTermService
{
    /** Returns all policy terms ordered for display, locale-aware. */
    public function getAll(): Collection
    {
        $terms = PolicyTerm::orderBy('order')->get();

        return PolicyTermResource::collection($terms)->collect();
    }
}
