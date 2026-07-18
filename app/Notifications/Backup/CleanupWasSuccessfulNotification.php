<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification as SpatieNotification;

class CleanupWasSuccessfulNotification extends SpatieNotification
{
    use HasAdminGreeting;
}
