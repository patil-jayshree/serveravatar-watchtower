<?php

namespace App\Actions\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Session;

class RevokeSession
{
    /**
     * Revoke a specific session.
     *
     * @param User $user
     * @param string $sessionId
     */
    public function execute(User $user, string $sessionId): void
    {
        // Prevent revoking current session
        if ($sessionId === Session::getId()) {
            return;
        }

        if (config('session.driver') === 'database') {
            \DB::table('sessions')
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->delete();
        }
    }
}
