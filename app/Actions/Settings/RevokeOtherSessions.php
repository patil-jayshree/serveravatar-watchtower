<?php

namespace App\Actions\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Session;

class RevokeOtherSessions
{
    /**
     * Revoke all sessions except the current one.
     */
    public function execute(User $user): void
    {
        $currentSessionId = Session::getId();

        if (config('session.driver') === 'database') {
            \DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId)
                ->delete();
        }
    }
}
