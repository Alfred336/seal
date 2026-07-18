<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification as SpatieNotification;

class BackupHasFailedNotification extends SpatieNotification
{
    use HasAdminGreeting;
}
