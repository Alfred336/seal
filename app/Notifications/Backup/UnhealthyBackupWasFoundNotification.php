<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification as SpatieNotification;

class UnhealthyBackupWasFoundNotification extends SpatieNotification
{
    use HasAdminGreeting;
}
