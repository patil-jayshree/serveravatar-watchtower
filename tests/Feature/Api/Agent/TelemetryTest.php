<?php

namespace Tests\Feature\Api\Agent;

use App\Actions\Agent\GenerateAgentToken;
use App\Models\AgentToken;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TelemetryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;
    protected AgentToken $token;
    protected string $rawToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $organization = \App\Models\Organization::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->project = Project::factory()->create([
            'organization_id' => $organization->id,
            'is_connected' => true,
        ]);

        // Generate a token via the action
        $tokenData = (new GenerateAgentToken)->execute($this->project);
        $this->rawToken = $tokenData['token'];

        $this->token = AgentToken::where('project_id', $this->project->id)->first();
    }

    protected function makePayload(array $overrides = []): array
    {
        return array_merge([
            'token' => $this->rawToken,
            'request_id' => 'req_' . Str::random(20),
            'method' => 'GET',
            'path' => '/api/users',
            'url' => 'https://example.com/api/users',
            'route_name' => 'users.index',
            'controller_action' => 'App\\Http\\Controllers\\UserController@index',
            'status_code' => 200,
            'duration_ms' => 142,
            'memory_bytes' => 10485760,
            'host' => 'example.com',
            'user_agent' => 'Laravel/10.0',
            'ip' => '192.168.1.1',
            'environment' => 'production',
            'content_type' => 'application/json',
            'requested_at' => now()->toIso8601String(),
        ], $overrides);
    }

    public function test_valid_token_can_send_telemetry(): void
    {
        $response = $this->postJson('/api/agent/requests', $this->makePayload());

        $response->assertStatus(201)
            ->assertJson(['received' => true])
            ->assertJsonStructure(['received', 'uuid']);

        $this->assertDatabaseHas('request_events', [
            'project_id' => $this->project->id,
            'method' => 'GET',
            'path' => '/api/users',
            'status_code' => 200,
            'duration_ms' => 142,
        ]);
    }

    public function test_invalid_token_rejected(): void
    {
        $response = $this->postJson('/api/agent/requests', $this->makePayload([
            'token' => 'wt_live_invalid_token',
        ]));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token not found or invalid.']);
    }

    public function test_revoked_token_rejected(): void
    {
        $rawToken = $this->rawToken;
        $this->token->revoke();

        $response = $this->postJson('/api/agent/requests', $this->makePayload([
            'token' => $rawToken,
        ]));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token has been revoked.']);
    }

    public function test_missing_token_rejected(): void
    {
        $payload = $this->makePayload();
        unset($payload['token']);

        $response = $this->postJson('/api/agent/requests', $payload);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token not provided.']);
    }

    public function test_missing_required_fields_rejected(): void
    {
        $payload = $this->makePayload();
        unset($payload['request_id'], $payload['method'], $payload['path'], $payload['status_code'], $payload['duration_ms']);

        $response = $this->postJson('/api/agent/requests', $payload);

        $response->assertStatus(422);
    }

    public function test_invalid_status_code_rejected(): void
    {
        $response = $this->postJson('/api/agent/requests', $this->makePayload([
            'status_code' => 999,
        ]));

        $response->assertStatus(422);
    }

    public function test_invalid_method_rejected(): void
    {
        $response = $this->postJson('/api/agent/requests', $this->makePayload([
            'method' => 'INVALID',
        ]));

        $response->assertStatus(422);
    }

    public function test_request_stored_under_correct_project(): void
    {
        $otherOrganization = \App\Models\Organization::factory()->create();
        $otherProject = Project::factory()->create([
            'organization_id' => $otherOrganization->id,
        ]);
        $otherTokenData = (new GenerateAgentToken)->execute($otherProject);

        $payload = $this->makePayload(['token' => $otherTokenData['token']]);

        $response = $this->postJson('/api/agent/requests', $payload);

        $response->assertStatus(201);

        // Should be stored under the OTHER project
        $this->assertDatabaseHas('request_events', [
            'project_id' => $otherProject->id,
            'method' => 'GET',
        ]);

        // Should NOT be stored under the original project
        $this->assertDatabaseMissing('request_events', [
            'project_id' => $this->project->id,
            'method' => 'GET',
        ]);
    }

    public function test_duplicate_request_id_gets_new_id(): void
    {
        $requestId = 'req_' . Str::random(20);

        // Send first request
        $this->postJson('/api/agent/requests', $this->makePayload(['request_id' => $requestId]))
            ->assertStatus(201);

        // Send duplicate
        $response = $this->postJson('/api/agent/requests', $this->makePayload(['request_id' => $requestId]))
            ->assertStatus(201);

        // Both should exist with different UUIDs
        $this->assertEquals(2, \App\Models\RequestEvent::where('request_id', 'like', $requestId . '%')->count());
    }

    public function test_token_cannot_access_other_project(): void
    {
        // User has two organizations with different projects
        $otherOrganization = \App\Models\Organization::factory()->create(['user_id' => $this->user->id]);
        $otherProject = Project::factory()->create(['organization_id' => $otherOrganization->id]);
        $otherTokenData = (new GenerateAgentToken)->execute($otherProject);

        // Token for OTHER project is used - it should send to OTHER project (not this project)
        $payload = $this->makePayload([
            'token' => $otherTokenData['token'],
        ]);

        $response = $this->postJson('/api/agent/requests', $payload);

        $response->assertStatus(201);

        // Event should be stored under OTHER project (the one the token belongs to)
        $this->assertDatabaseHas('request_events', [
            'project_id' => $otherProject->id,
            'method' => 'GET',
        ]);

        // NOT under the original project
        $this->assertDatabaseMissing('request_events', [
            'project_id' => $this->project->id,
            'method' => 'GET',
        ]);
    }
}
