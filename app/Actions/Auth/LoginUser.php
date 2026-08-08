<?php

namespace App\Actions\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginUser
{
    /**
     * Attempt to authenticate the user.
     *
     * @param array{email: string, password: string, remember?: bool} $credentials
     * @throws ValidationException
     */
    public function execute(array $credentials, Request $request): User|RedirectResponse
    {
        $remember = $credentials['remember'] ?? false;

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        if (! $user) {
            throw ValidationException::withMessages([
                'login' => ['Unable to authenticate. Please try again.'],
            ]);
        }

        // Check if user is suspended (handle null status)
        if ($user->status && $user->status->isSuspended()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => ['Your account has been suspended. Please contact support.'],
            ]);
        }

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        return $user;
    }
}
