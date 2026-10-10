<?php

namespace App\Enums;

enum OrcidVerificationState: string
{
    case UNVERIFIED = 'UNVERIFIED';
    case AUTHENTICATED = 'AUTHENTICATED';
}
