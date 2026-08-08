<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\RevokeSession;
use App\Actions\Settings\RevokeOtherSessions;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    /**
     * Display the sessions settings page.
     */
    public function index()
    {
        $sessions = $this->getSessions();
        
        return view('settings.sessions', [
            'sessions' => $sessions,
            'currentSessionId' => Session::getId(),
        ]);
    }

    /**
     * Revoke a specific session.
     */
    public function destroy(Request $request, RevokeSession $action, string $sessionId): RedirectResponse
    {
        $action->execute(Auth::user(), $sessionId);

        return back()->with('status', 'Session revoked successfully.');
    }

    /**
     * Revoke all sessions except the current one.
     */
    public function revokeAll(RevokeOtherSessions $action): RedirectResponse
    {
        $action->execute(Auth::user());

        return back()->with('status', 'All other sessions revoked successfully.');
    }

    /**
     * Get the user's active sessions.
     */
    protected function getSessions(): array
    {
        $sessions = [];
        
        if (config('session.driver') === 'database') {
            $dbSessions = \DB::table('sessions')
                ->where('user_id', Auth::id())
                ->orderByDesc('last_activity')
                ->get();

            foreach ($dbSessions as $session) {
                $sessions[$session->id] = [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'payload' => $session->payload,
                ];
            }
        }

        return $sessions;
    }
}
