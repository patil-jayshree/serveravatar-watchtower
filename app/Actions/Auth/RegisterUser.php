<?php

namespace App\Actions\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    /**
     * Register a new user.
     *
     * @param array{name: string, email: string, password: string, timezone?: string, locale?: string} $data
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'timezone' => $data['timezone'] ?? config('app.timezone'),
                'locale' => $data['locale'] ?? config('app.locale'),
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(), // Auto-verify for development
            ]);

            // $user->sendEmailVerificationNotification(); // Disabled for development

            return $user;
        });
    }
}
