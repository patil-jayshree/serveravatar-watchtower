<?php

namespace App\Actions\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RemoveAvatar
{
    /**
     * Remove the user's avatar.
     */
    public function execute(User $user): User
    {
        // Delete avatar file if exists
        if ($user->avatar_path) {
            Storage::disk('avatars')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return $user->fresh();
    }
}
