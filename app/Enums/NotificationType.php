<?php

namespace App\Enums;

enum NotificationType: string
{
    case SUCCESS = 'SUCCESS';
    case FAILURE = 'FAILURE';
    case REFUND = 'REFUND';
}
