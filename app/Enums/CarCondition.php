<?php

namespace App\Enums;

/** Defines whether a car is new or previously owned. */
enum CarCondition: string
{
    case New = 'new';
    case Used = 'used';
}
