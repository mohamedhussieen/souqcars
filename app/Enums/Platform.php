<?php

namespace App\Enums;

/** Defines the supported client platforms for app configuration. */
enum Platform: string
{
    case Android = 'android';
    case Ios = 'ios';
}
