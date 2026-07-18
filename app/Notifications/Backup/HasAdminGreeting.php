<?php

namespace App\Notifications\Backup;

use Illuminate\Notifications\Messages\MailMessage;

trait HasAdminGreeting
{
    /**
     * Build the mail representation of the notification.
     * Intercepts the parent mail generation and adds a personalized greeting.
     */
    public function toMail($notifiable = null): MailMessage
    {
        $mailMessage = parent::toMail($notifiable);

        if ($notifiable && isset($notifiable->name)) {
            $mailMessage->greeting(__('Hello, :name!', ['name' => $notifiable->name]));
        }

        return $mailMessage;
    }
}
