<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case SUBMITTED = 'SUBMITTED';
    case CORRECTION_REQUIRED = 'CORRECTION_REQUIRED';
    case VERIFIED = 'VERIFIED';
    case CANCELLED = 'CANCELLED';
}
