<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the notification's mail subject.
     */
    public function subject(): string
    {
        return 'Verify Your Email Address';
    }

    /**
     * Get the notification's mail greeting.
     */
    public function greeting(): string
    {
        return 'Hello!';
    }

    /**
     * Get the notification's mail intro lines.
     */
    public function introLines(): array
    {
        return [
            'Thank you for registering with ServerAvatar Watchtower. Please verify your email address by clicking the button below.',
        ];
    }

    /**
     * Get the verification URL.
     */
    public function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the notification's mail message.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject())
            ->greeting($this->greeting())
            ->line('Thank you for registering with ServerAvatar Watchtower. Please verify your email address by clicking the button below.')
            ->action('Verify Email Address', $this->verificationUrl($notifiable))
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
