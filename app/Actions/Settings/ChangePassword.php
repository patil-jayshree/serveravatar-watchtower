<?php

namespace App\Actions\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePassword
{
    /**
     * Change the user's password.
     *
     * @param User $user
     * @param array{current_password: string, password: string, password_confirmation: string} $data
     */
    public function execute(User $user, array $data): User
    {
        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }
}
