<?php

namespace App\Enums;

enum ProjectRequestStatus: string
{
    case New = 'new';
    case Reviewed = 'reviewed';
    case InProgress = 'in_progress';
    case Closed = 'closed';
}
