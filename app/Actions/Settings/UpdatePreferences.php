<?php

namespace App\Actions\Settings;

use App\Models\User;

class UpdatePreferences
{
    /**
     * Update the user's preferences.
     *
     * @param User $user
     * @param array{timezone?: string, locale?: string, theme_preference?: string} $data
     */
    public function execute(User $user, array $data): User
    {
        $allowedTimezones = timezone_identifiers_list();
        $allowedLocales = ['en', 'es', 'fr', 'de', 'pt', 'zh', 'ja'];
        $allowedThemes = ['light', 'dark', 'system'];

        $preferences = [];

        if (isset($data['timezone']) && in_array($data['timezone'], $allowedTimezones)) {
            $preferences['timezone'] = $data['timezone'];
        }

        if (isset($data['locale']) && in_array($data['locale'], $allowedLocales)) {
            $preferences['locale'] = $data['locale'];
        }

        if (isset($data['theme_preference']) && in_array($data['theme_preference'], $allowedThemes)) {
            $preferences['theme_preference'] = $data['theme_preference'];
        }

        $user->update($preferences);

        return $user->fresh();
    }
}
