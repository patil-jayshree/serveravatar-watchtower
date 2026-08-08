<?php

namespace App\Actions\Settings;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateAvatar
{
    /**
     * Update the user's avatar.
     *
     * @param User $user
     * @param UploadedFile $file
     */
    public function execute(User $user, UploadedFile $file): User
    {
        // Delete old avatar if exists
        if ($user->avatar_path) {
            Storage::disk('avatars')->delete($user->avatar_path);
        }

        // Generate unique filename
        $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Store the new avatar
        $file->storeAs('/', $filename, ['disk' => 'avatars']);

        // Update user
        $user->update(['avatar_path' => $filename]);

        return $user->fresh();
    }
}
