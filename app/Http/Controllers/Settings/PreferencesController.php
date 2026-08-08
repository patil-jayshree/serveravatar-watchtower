<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\UpdatePreferences;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePreferencesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PreferencesController extends Controller
{
    /**
     * Display the preferences settings page.
     */
    public function edit()
    {
        $timezones = timezone_identifiers_list();
        sort($timezones);
        
        return view('settings.preferences', [
            'timezones' => $timezones,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's preferences.
     */
    public function update(UpdatePreferencesRequest $request, UpdatePreferences $action): RedirectResponse
    {
        $action->execute(Auth::user(), $request->validated());

        return back()->with('status', 'Preferences updated successfully.');
    }
}
