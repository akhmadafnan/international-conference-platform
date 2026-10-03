<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case PENDING = 'PENDING';
    case PAYMENT_PENDING = 'PAYMENT_PENDING';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
}
