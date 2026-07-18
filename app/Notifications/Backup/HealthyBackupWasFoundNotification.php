<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification as SpatieNotification;

class HealthyBackupWasFoundNotification extends SpatieNotification
{
    use HasAdminGreeting;
}
