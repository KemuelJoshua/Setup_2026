<?php

namespace App\Enums;

enum SchoolYearStatus: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
    case PLANNED = 'planned';
}
