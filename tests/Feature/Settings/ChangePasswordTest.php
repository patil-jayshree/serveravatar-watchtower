<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
        ]);
    }

    public function test_user_can_view_security_settings(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('settings.security'));

        $response->assertStatus(200);
        $response->assertSee('Change Password');
    }

    public function test_user_can_change_password(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.security.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ]);

        $response->assertRedirect();
        $this->user->refresh();
        $this->assertTrue(Hash::check('NewPassword456!', $this->user->password));
    }

    public function test_current_password_is_required(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.security.update'), [
                'current_password' => '',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_incorrect_current_password_fails(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.security.update'), [
                'current_password' => 'WrongPassword!',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.security.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'DifferentPassword!',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_new_password_must_meet_requirements(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.security.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_requires_minimum_length(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.security.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'Ab1!',
                'password_confirmation' => 'Ab1!',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_unauthenticated_user_cannot_access_security_settings(): void
    {
        $response = $this->get(route('settings.security'));

        $response->assertRedirect('/login');
    }
}
