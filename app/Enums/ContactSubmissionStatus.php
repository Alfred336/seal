<?php

namespace App\Enums;

enum ContactSubmissionStatus: string
{
    case New = 'new';
    case Reviewed = 'reviewed';
    case Closed = 'closed';
}
