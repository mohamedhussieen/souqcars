<?php

namespace App\Traits;

/** Resolves bilingual _ar/_en fields to a single 'name' based on the current app locale. */
trait HasLocalizedFields
{
    /** Returns the localized value of a bilingual field pair (e.g., name_ar / name_en). */
    protected function localizeField(string $field): string
    {
        $locale = app()->getLocale();
        $column = "{$field}_{$locale}";

        return $this->resource->{$column} ?? $this->resource->{$field . '_ar'} ?? '';
    }
}
