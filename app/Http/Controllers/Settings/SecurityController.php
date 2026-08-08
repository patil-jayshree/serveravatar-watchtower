<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\ChangePassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    /**
     * Display the security settings page.
     */
    public function edit()
    {
        return view('settings.security');
    }

    /**
     * Change the user's password.
     */
    public function update(ChangePasswordRequest $request, ChangePassword $action): RedirectResponse
    {
        $action->execute(Auth::user(), $request->validated());

        return back()->with('status', 'Password changed successfully.');
    }
}
