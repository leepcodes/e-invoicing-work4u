<?php

namespace App\Enums;

enum SubcriptionStatusEnum:  string
{
    case ACTIVE = 'active';
    case TRIALING = 'trialing';
    case PAST_DUE = 'past_due';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
