<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\UpdatePreferences;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePreferencesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PreferencesController extends Controller
{
    /**
     * Display the preferences settings page.
     */
    public function edit(): Response
    {
        $timezones = timezone_identifiers_list();
        sort($timezones);

        return Inertia::render('Settings/Preferences', [
            'timezones' => $timezones,
            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'timezone' => Auth::user()->timezone,
                'theme' => Auth::user()->theme ?? 'system',
            ],
            'status' => session('status'),
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
