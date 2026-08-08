<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);
    }

    public function test_user_can_view_profile_settings(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('settings.profile'));

        $response->assertStatus(200);
        $response->assertSee('Test User');
        $response->assertSee('test@example.com');
    }

    public function test_user_can_update_name(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.profile.update'), [
                'name' => 'New Name',
                'email' => $this->user->email,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_user_can_update_email(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->user)
            ->put(route('settings.profile.update'), [
                'name' => $this->user->name,
                'email' => 'newemail@example.com',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email' => 'newemail@example.com',
        ]);
        // Email should be unverified after change
        $this->user->refresh();
        $this->assertNull($this->user->email_verified_at);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('settings.profile.update'), [
                'name' => $this->user->name,
                'email' => 'other@example.com',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
            'email' => 'other@example.com',
        ]);
    }

    public function test_name_is_required(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.profile.update'), [
                'name' => '',
                'email' => $this->user->email,
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_email_must_be_valid(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('settings.profile.update'), [
                'name' => $this->user->name,
                'email' => 'not-an-email',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_unauthenticated_user_cannot_access_profile_settings(): void
    {
        $response = $this->get(route('settings.profile'));

        $response->assertRedirect('/login');
    }
}
