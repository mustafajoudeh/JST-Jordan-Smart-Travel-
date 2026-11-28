<?php

namespace App\Enums;

enum Role: string
{
    case SystemAdmin = 'system_admin';
    case ContentAdmin = 'content_admin';
    case Provider = 'provider';
    case Traveler = 'traveler';
}
