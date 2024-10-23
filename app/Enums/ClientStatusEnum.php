<?php

namespace App\Enums;

enum ClientStatusEnum: string
{
    case IN_PROGRESS = 'IN_PROGRESS';
    case CLOSED = 'CLOSED';
    case FINISHED = 'FINISHED';
    case CANCELED = 'CANCELED';
}