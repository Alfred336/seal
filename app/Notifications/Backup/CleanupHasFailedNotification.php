<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification as SpatieNotification;

class CleanupHasFailedNotification extends SpatieNotification
{
    use HasAdminGreeting;
}
