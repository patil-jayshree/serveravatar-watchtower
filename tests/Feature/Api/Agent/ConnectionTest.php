<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Agent;

use App\Actions\Agent\GenerateAgentToken;
use App\Enums\Agent\AgentTokenStatus;
use App\Models\AgentToken;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConnectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Organization $organization;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['user_id' => $this->user->id]);
        $this->project = Project::factory()->create(['organization_id' => $this->organization->id]);
    }

    public function test_valid_token_authenticates(): void
    {
        // Generate a token for the project
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        $rawToken = $result['token'];

        // Call the API
        $response = $this->postJson('/api/agent/connection', [
            'token' => $rawToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'project' => [
                    'id' => $this->project->uuid,
                    'name' => $this->project->name,
                ],
            ]);

        // Ensure project is marked as connected
        $this->project->refresh();
        $this->assertTrue($this->project->is_connected);
        $this->assertNotNull($this->project->last_connected_at);
    }

    public function test_invalid_token_fails(): void
    {
        $response = $this->postJson('/api/agent/connection', [
            'token' => 'wt_live_invalid_token_that_does_not_exist',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_revoked_token_fails(): void
    {
        // Generate a token
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);
        $rawToken = $result['token'];

        // Revoke it
        $token = AgentToken::where('project_id', $this->project->id)->first();
        $token->update(['status' => AgentTokenStatus::Revoked]);

        // Call the API
        $response = $this->postJson('/api/agent/connection', [
            'token' => $rawToken,
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Token has been revoked.',
            ]);
    }

    public function test_malformed_token_format_fails(): void
    {
        $response = $this->postJson('/api/agent/connection', [
            'token' => 'not_a_valid_token_format',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_missing_token_fails(): void
    {
        $response = $this->postJson('/api/agent/connection', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    }

    public function test_token_without_existing_record_fails(): void
    {
        // Use a valid format token but no matching record
        $response = $this->postJson('/api/agent/connection', [
            'token' => 'wt_live_abcdefghijklmnopqrstuvwxyz123456',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_connection_updates_project_connected_status(): void
    {
        // Ensure project starts as not connected
        $this->assertFalse($this->project->is_connected);

        // Generate and verify connection
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        $this->postJson('/api/agent/connection', [
            'token' => $result['token'],
        ]);

        $this->project->refresh();
        $this->assertTrue($this->project->is_connected);
        $this->assertNotNull($this->project->last_connected_at);
    }

    public function test_response_does_not_expose_token(): void
    {
        $action = new GenerateAgentToken();
        $result = $action->execute($this->project);

        $response = $this->postJson('/api/agent/connection', [
            'token' => $result['token'],
        ]);

        $response->assertStatus(200);

        $content = $response->getContent();

        // Ensure raw token is not in response
        $this->assertStringNotContainsString('wt_live_', $content);
        $this->assertStringNotContainsString($result['token'], $content);
    }
}
