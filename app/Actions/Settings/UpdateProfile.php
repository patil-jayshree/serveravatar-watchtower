<?php

namespace App\Actions\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateProfile
{
    /**
     * Update the user's profile.
     *
     * @param User $user
     * @param array{name?: string, email?: string} $data
     */
    public function execute(User $user, array $data): User
    {
        $emailChanged = isset($data['email']) && $data['email'] !== $user->email;

        if ($emailChanged) {
            // Mark as unverified and resend notification
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        }

        $user->update($data);

        return $user->fresh();
    }
}
