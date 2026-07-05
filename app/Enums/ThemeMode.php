<?php

namespace App\Enums;

/** Defines the supported UI theme modes for user preferences. */
enum ThemeMode: string
{
    case Light = 'light';
    case Dark = 'dark';

    /** Returns all valid theme mode values as a plain array. */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
