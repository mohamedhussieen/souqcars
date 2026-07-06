<?php

namespace App\Traits;

/**
 * Registers a single-file media collection with an "optimized" conversion:
 * same dimensions as the original, re-encoded at high quality to shrink file size.
 */
trait HasOptimizedMedia
{
    /** Registers the given collection as single-file and adds the optimized conversion to it. */
    protected function registerOptimizedMediaCollection(string $collectionName): void
    {
        $this->addMediaCollection($collectionName)->singleFile();
    }

    /** Defines the "optimized" conversion: original dimensions, quality-based size reduction. Call from registerMediaConversions(). */
    protected function registerOptimizedConversion(): void
    {
        $this->addMediaConversion('optimized')
            ->quality(80)
            ->nonQueued();
    }
}
