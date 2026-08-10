<?php

namespace Tests\Feature\Agent;

use App\Actions\Agent\GenerateAgentToken;
use App\Actions\Agent\RegenerateAgentToken;
use App\Actions\Agent\RevokeAgentToken;
use App\Enums\Agent\AgentTokenStatus;
use App\Models\AgentToken;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgentTokenTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Organization $organization;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->organization = Organization::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->project = Project::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
    }

    // ============================================
    // Generation Tests
    // ============================================

    public function test_authenticated_user_can_generate_token_for_their_project(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('organizations.projects.agent.store', [$this->organization, $this->project]));

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'masked',
            'token',
        ]);

        $this->assertDatabaseHas('agent_tokens', [
            'project_id' => $this->project->id,
            'status' => AgentTokenStatus::Active->value,
        ]);
    }

    public function test_token_is_securely_generated(): void
    {
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        // Token should start with wt_live_
        $this->assertStringStartsWith('wt_live_', $result['token']);

        // Token should be 51 characters (wt_live_ = 8 + 44 random)
        $this->assertEquals(52, strlen($result['token']));

        // Token hash should be stored, not raw token
        $this->assertDatabaseHas('agent_tokens', [
            'project_id' => $this->project->id,
        ]);

        $storedToken = AgentToken::where('project_id', $this->project->id)->first();
        $this->assertNotEquals($result['token'], $storedToken->token_hash);
        $this->assertEquals(64, strlen($storedToken->token_hash)); // SHA256 hash length
    }

    public function test_token_belongs_to_correct_project(): void
    {
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        $token = AgentToken::where('project_id', $this->project->id)->first();

        $this->assertNotNull($token);
        $this->assertEquals($this->project->id, $token->project_id);
    }

    public function test_only_one_active_token_exists(): void
    {
        // Generate first token
        $action = new GenerateAgentToken();
        $result1 = $action->execute($this->project);

        // Generate second time - should return existing token
        $result2 = $action->execute($this->project);

        // Should still have only one token
        $this->assertEquals(1, AgentToken::where('project_id', $this->project->id)->count());

        // Second result should not have raw token
        $this->assertNull($result2['token']);
    }

    // ============================================
    // Display Tests
    // ============================================

    public function test_newly_generated_token_is_returned(): void
    {
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        // Token should be returned on first generation
        $this->assertNotNull($result['token']);
        $this->assertStringStartsWith('wt_live_', $result['token']);
    }

    public function test_token_is_masked_after_initial_generation(): void
    {
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        // The masked token should be returned
        $this->assertStringContainsString('•', $result['masked']);
        $this->assertStringStartsWith('wt_live_', $result['masked']);

        // Verify the masked token doesn't contain the actual token
        $this->assertNotEquals($result['token'], $result['masked']);
    }

    public function test_raw_token_not_exposed_in_json_response(): void
    {
        $action = new GenerateAgentToken();
        $action->execute($this->project);

        // Get token via JSON API
        $response = $this->actingAs($this->user)
            ->getJson(route('organizations.projects.agent.show', [$this->organization, $this->project]));

        $response->assertStatus(200);
        $response->assertJsonMissing(['token' => 'wt_live_']);
    }

    // ============================================
    // Regeneration Tests
    // ============================================

    public function test_user_can_regenerate_token(): void
    {
        // Generate initial token
        $action = new GenerateAgentToken();
        $result1 = $action->execute($this->project);

        // Regenerate
        $regenerateAction = new RegenerateAgentToken();
        $result2 = $regenerateAction->execute($this->project);

        $this->assertNotNull($result2['token']);
        $this->assertStringStartsWith('wt_live_', $result2['token']);
    }

    public function test_old_token_becomes_invalid_after_regeneration(): void
    {
        // Generate initial token
        $action = new GenerateAgentToken();
        $result1 = $action->execute($this->project);

        $oldToken = AgentToken::where('project_id', $this->project->id)
            ->where('status', AgentTokenStatus::Active)
            ->first();

        // Regenerate
        $regenerateAction = new RegenerateAgentToken();
        $regenerateAction->execute($this->project);

        // Old token should be revoked
        $oldToken->refresh();
        $this->assertEquals(AgentTokenStatus::Revoked, $oldToken->status);
    }

    public function test_new_token_is_different_after_regeneration(): void
    {
        // Generate initial token
        $action = new GenerateAgentToken();
        $result1 = $action->execute($this->project);

        // Regenerate
        $regenerateAction = new RegenerateAgentToken();
        $result2 = $regenerateAction->execute($this->project);

        // Tokens should be different
        $this->assertNotEquals($result1['token'], $result2['token']);
    }

    public function test_new_token_becomes_active_after_regeneration(): void
    {
        // Generate initial token
        $action = new GenerateAgentToken();
        $action->execute($this->project);

        // Regenerate
        $regenerateAction = new RegenerateAgentToken();
        $result = $regenerateAction->execute($this->project);

        $this->assertEquals('active', $result['status']);

        // Should have one active token
        $activeTokens = AgentToken::where('project_id', $this->project->id)
            ->where('status', AgentTokenStatus::Active)
            ->count();
        $this->assertEquals(1, $activeTokens);
    }

    // ============================================
    // Revocation Tests
    // ============================================

    public function test_user_can_revoke_token(): void
    {
        // Generate token first
        $action = new GenerateAgentToken();
        $action->execute($this->project);

        // Revoke
        $revokeAction = new RevokeAgentToken();
        $revokeAction->execute($this->project);

        // Token should be revoked
        $token = AgentToken::where('project_id', $this->project->id)->first();
        $this->assertEquals(AgentTokenStatus::Revoked, $token->status);
    }

    public function test_revoked_token_cannot_be_verified(): void
    {
        // Generate token
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        $token = AgentToken::where('project_id', $this->project->id)->first();

        // Revoke
        $revokeAction = new RevokeAgentToken();
        $revokeAction->execute($this->project);

        // Refresh token from database
        $token->refresh();

        // Token should not verify
        $this->assertFalse($token->verify($result['token']));
    }

    public function test_project_remains_active_after_revocation(): void
    {
        // Generate token
        $action = new GenerateAgentToken();
        $action->execute($this->project);

        // Revoke
        $revokeAction = new RevokeAgentToken();
        $revokeAction->execute($this->project);

        // Project should still exist
        $this->assertDatabaseHas('projects', ['id' => $this->project->id]);
    }

    public function test_user_can_generate_new_token_after_revocation(): void
    {
        // Generate and revoke first token
        $action = new GenerateAgentToken();
        $action->execute($this->project);

        $revokeAction = new RevokeAgentToken();
        $revokeAction->execute($this->project);

        // Generate new token
        $generateAction = new GenerateAgentToken();
        $result = $generateAction->execute($this->project);

        // Should have new token
        $this->assertNotNull($result['token']);
        $this->assertEquals('active', $result['status']);
    }

    // ============================================
    // Authorization Tests
    // ============================================

    public function test_user_cannot_generate_token_for_another_users_project(): void
    {
        $otherUser = User::factory()->create();
        $otherOrganization = Organization::factory()->create(['user_id' => $otherUser->id]);
        $otherProject = Project::factory()->create(['organization_id' => $otherOrganization->id]);

        $response = $this->actingAs($this->user)
            ->postJson(route('organizations.projects.agent.store', [$otherOrganization, $otherProject]));

        $response->assertStatus(403);
    }

    public function test_user_cannot_regenerate_another_users_project_token(): void
    {
        $otherUser = User::factory()->create();
        $otherOrganization = Organization::factory()->create(['user_id' => $otherUser->id]);
        $otherProject = Project::factory()->create(['organization_id' => $otherOrganization->id]);

        $response = $this->actingAs($this->user)
            ->putJson(route('organizations.projects.agent.update', [$otherOrganization, $otherProject]));

        $response->assertStatus(403);
    }

    public function test_user_cannot_revoke_another_users_project_token(): void
    {
        $otherUser = User::factory()->create();
        $otherOrganization = Organization::factory()->create(['user_id' => $otherUser->id]);
        $otherProject = Project::factory()->create(['organization_id' => $otherOrganization->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('organizations.projects.agent.destroy', [$otherOrganization, $otherProject]));

        $response->assertStatus(403);
    }

    public function test_organization_ownership_is_enforced(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->postJson(route('organizations.projects.agent.store', [$this->organization, $this->project]));

        $response->assertStatus(403);
    }

    // ============================================
    // Security Tests
    // ============================================

    public function test_unauthenticated_user_cannot_access_agent_token_routes(): void
    {
        // POST - unauthenticated POST to web route redirects to login (302)
        $this->postJson(route('organizations.projects.agent.store', [$this->organization, $this->project]))
            ->assertStatus(302);

        // GET - web routes redirect to login (302)
        $this->get(route('organizations.projects.agent.show', [$this->organization, $this->project]))
            ->assertStatus(302);

        // PUT - unauthenticated PUT redirects to login (302)
        $this->putJson(route('organizations.projects.agent.update', [$this->organization, $this->project]))
            ->assertStatus(302);

        // DELETE - unauthenticated DELETE redirects to login (302)
        $this->deleteJson(route('organizations.projects.agent.destroy', [$this->organization, $this->project]))
            ->assertStatus(302);
    }

    public function test_token_verification_works_correctly(): void
    {
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        $token = AgentToken::where('project_id', $this->project->id)->first();

        // Correct token should verify
        $this->assertTrue($token->verify($result['token']));

        // Wrong token should not verify
        $this->assertFalse($token->verify('wt_live_invalid_token'));
    }

    public function test_revoked_token_does_not_verify(): void
    {
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        $token = AgentToken::where('project_id', $this->project->id)->first();
        $rawToken = $result['token'];

        $revokeAction = new RevokeAgentToken();
        $revokeAction->execute($this->project);

        $token->refresh();

        $this->assertFalse($token->verify($rawToken));
    }

    public function test_token_has_proper_prefix(): void
    {
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        $token = AgentToken::where('project_id', $this->project->id)->first();

        $this->assertEquals('wt_live_', $token->token_prefix);
        $this->assertStringStartsWith('wt_live_', $result['token']);
    }
}
