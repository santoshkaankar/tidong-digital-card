<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class RegistrationVerifyEmail extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $notifiable->getKey(),
            'hash' => sha1((string) $notifiable->email),
        ]);
        return (new MailMessage)
            ->subject('Verify your TIDONG email address')
            ->line('Please verify the email address linked to your registration.')
            ->action('Verify email', $url)
            ->line('The link expires in 60 minutes.');
    }
}
