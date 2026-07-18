<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification as SpatieNotification;

class BackupWasSuccessfulNotification extends SpatieNotification
{
    use HasAdminGreeting;
}
