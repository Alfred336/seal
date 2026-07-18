<?php

namespace App\Notifications;

use App\Enums\Role;
use App\Models\User;
use Spatie\Backup\Notifications\Notifiable as SpatieNotifiable;

class BackupNotifiable extends SpatieNotifiable
{
    /**
     * Send the given notification to all administrators individually.
     * Falls back to default notification routing if query fails or no administrators exist.
     *
     * @param  mixed  $notification
     * @return void
     */
    public function notify($notification): void
    {
        try {
            $admins = User::role(Role::Admin->value)->get();

            if ($admins->isNotEmpty()) {
                foreach ($admins as $admin) {
                    $admin->notify($notification);
                }
                return;
            }
        } catch (\Throwable $e) {
            // Fallback in case of database connectivity issues during CLI boots
        }

        parent::notify($notification);
    }

    /**
     * Route notifications for the mail channel.
     * Returns an array of emails for all users with the 'admin' role.
     * Fallback to the configured default email if no admin users exist or query fails.
     *
     * @return string|array
     */
    public function routeNotificationForMail(): string|array
    {
        try {
            $adminEmails = User::role(Role::Admin->value)
                ->pluck('email')
                ->filter()
                ->toArray();

            if (! empty($adminEmails)) {
                return $adminEmails;
            }
        } catch (\Throwable $e) {
            // Fallback in case of database connectivity issues during boot
        }

        return parent::routeNotificationForMail();
    }
}
