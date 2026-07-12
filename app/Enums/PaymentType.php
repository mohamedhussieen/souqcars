<?php

namespace App\Enums;

/** Defines the payment options accepted for a car listing. */
enum PaymentType: string
{
    case Cash = 'cash';
    case Installment = 'installment';
    case Both = 'both';
}
