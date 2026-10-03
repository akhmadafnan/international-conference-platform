<?php

namespace App\Enums;

enum RegistrationActivityStatus: string
{
    case ENTITLED = 'ENTITLED';
    case CANCELLED = 'CANCELLED';
}
