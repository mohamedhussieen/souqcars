<?php

namespace App\Enums;

/** Defines what a tapped ad navigates to. */
enum AdActionType: string
{
    case Car = 'car';
    case Url = 'url';
    case Screen = 'screen';
}
