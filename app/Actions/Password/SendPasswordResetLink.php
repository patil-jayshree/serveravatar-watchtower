<?php

namespace App\Actions\Password;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class SendPasswordResetLink
{
    /**
     * Send a password reset link to the given user.
     *
     * @throws \Exception
     */
    public function execute(string $email): string
    {
        $user = User::where('email', $email)->first();

        // Always return success message to prevent email enumeration
        // Even if user doesn't exist, we show the same message
        if (! $user) {
            return Password::RESET_LINK_SENT;
        }

        // Generate a new token
        $token = Str::random(64);

        // Store the token in the password_reset_tokens table
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        // Send the notification
        $user->sendPasswordResetNotification($token);

        return Password::RESET_LINK_SENT;
    }
}
