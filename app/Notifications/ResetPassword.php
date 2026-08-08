<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     */
    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's mail subject.
     */
    public function subject(): string
    {
        return 'Reset Your Password';
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
            'You are receiving this email because we received a password reset request for your account.',
        ];
    }

    /**
     * Get the reset URL.
     */
    public function resetUrl(): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->notifiable->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Get the notification's mail message.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject())
            ->greeting($this->greeting())
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $this->resetUrl())
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
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
