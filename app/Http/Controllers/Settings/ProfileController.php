<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\UpdateProfile;
use App\Actions\Settings\UpdateAvatar;
use App\Actions\Settings\RemoveAvatar;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the profile settings page.
     */
    public function edit(): Response
    {
        $user = Auth::user();
        return Inertia::render('Settings/Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    /**
     * Update the profile.
     */
    public function update(UpdateProfileRequest $request, UpdateProfile $action): RedirectResponse
    {
        $action->execute(Auth::user(), $request->validated());

        return back()->with('status', 'Profile updated successfully.');
    }

    /**
     * Upload a new avatar.
     */
    public function uploadAvatar(UpdateAvatar $action): RedirectResponse
    {
        $action->execute(Auth::user(), request()->file('avatar'));

        return back()->with('status', 'Avatar updated successfully.');
    }

    /**
     * Remove the avatar.
     */
    public function removeAvatar(RemoveAvatar $action): RedirectResponse
    {
        $action->execute(Auth::user());

        return back()->with('status', 'Avatar removed successfully.');
    }
}
