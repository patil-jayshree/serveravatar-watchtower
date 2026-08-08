<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreferencesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'timezone' => 'UTC',
            'locale' => 'en',
            'theme_preference' => 'system',
            'email_verified_at' => now(),
        ]);
    }

    public function test_user_can_view_preferences_settings(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('settings.preferences'));

        $response->assertStatus(200);
        $response->assertSee('Preferences');
        $response->assertSee('UTC');
    }

    public function test_user_can_update_timezone(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.preferences.update'), [
                'timezone' => 'America/New_York',
                'locale' => $this->user->locale,
                'theme_preference' => $this->user->theme_preference,
            ]);

        $response->assertRedirect();
        $this->user->refresh();
        $this->assertEquals('America/New_York', $this->user->timezone);
    }

    public function test_user_can_update_locale(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.preferences.update'), [
                'timezone' => $this->user->timezone,
                'locale' => 'fr',
                'theme_preference' => $this->user->theme_preference,
            ]);

        $response->assertRedirect();
        $this->user->refresh();
        $this->assertEquals('fr', $this->user->locale);
    }

    public function test_user_can_update_theme_preference(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.preferences.update'), [
                'timezone' => $this->user->timezone,
                'locale' => $this->user->locale,
                'theme_preference' => 'dark',
            ]);

        $response->assertRedirect();
        $this->user->refresh();
        $this->assertEquals('dark', $this->user->theme_preference);
    }

    public function test_invalid_timezone_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.preferences.update'), [
                'timezone' => 'Invalid/Timezone',
                'locale' => $this->user->locale,
                'theme_preference' => $this->user->theme_preference,
            ]);

        $response->assertSessionHasErrors('timezone');
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.preferences.update'), [
                'timezone' => $this->user->timezone,
                'locale' => 'invalid_locale',
                'theme_preference' => $this->user->theme_preference,
            ]);

        $response->assertSessionHasErrors('locale');
    }

    public function test_invalid_theme_preference_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.preferences.update'), [
                'timezone' => $this->user->timezone,
                'locale' => $this->user->locale,
                'theme_preference' => 'invalid_theme',
            ]);

        $response->assertSessionHasErrors('theme_preference');
    }

    public function test_unauthenticated_user_cannot_access_preferences(): void
    {
        $response = $this->get(route('settings.preferences'));

        $response->assertRedirect('/login');
    }
}
