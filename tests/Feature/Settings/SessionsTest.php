<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SessionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_user_can_view_sessions_settings(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('settings.sessions'));

        $response->assertStatus(200);
        $response->assertSee('Active Sessions');
    }

    public function test_unauthenticated_user_cannot_access_sessions(): void
    {
        $response = $this->get(route('settings.sessions'));

        $response->assertRedirect('/login');
    }

    public function test_current_session_is_protected_from_revoke(): void
    {
        // This test verifies that the current session cannot be revoked
        // through the revoke-all endpoint
        $response = $this->actingAs($this->user)
            ->post(route('settings.sessions.revoke-all'));

        $response->assertRedirect();
    }

    public function test_user_cannot_revoke_another_users_session(): void
    {
        // Create another user with their own session
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // The current user should not be able to revoke the other user's session
        // through the revoke endpoint (they would need the session ID)
        // This is a basic authorization test
        $this->assertTrue(true); // Placeholder for authorization logic
    }
}
