<?php

namespace App\Actions\Password;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPassword
{
    /**
     * Reset the user's password.
     *
     * @param array{email: string, token: string, password: string} $data
     */
    public function execute(array $data): RedirectResponse
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return redirect()->back()
                ->withInput(['email' => $data['email']])
                ->withErrors(['email' => 'Unable to find a user with that email address.']);
        }

        // Clear any existing password reset tokens
        \DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        // Update the password
        $user->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('login')
            ->with('status', 'Your password has been reset. Please log in with your new password.');
    }
}
