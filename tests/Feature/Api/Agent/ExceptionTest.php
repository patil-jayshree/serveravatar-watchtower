<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Agent;

use App\Actions\Agent\GenerateAgentToken;
use App\Models\AgentToken;
use App\Models\Organization;
use App\Models\Project;
use App\Models\RequestEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExceptionTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;
    protected Project $project;
    protected AgentToken $token;
    protected string $rawToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();

        $this->project = Project::factory()->create([
            'organization_id' => $this->organization->id,
            'is_connected' => true,
        ]);

        // Generate a token
        $tokenData = (new GenerateAgentToken)->execute($this->project);
        $this->rawToken = $tokenData['token'];

        $this->token = AgentToken::where('project_id', $this->project->id)->first();
    }

    protected function makePayload(array $overrides = []): array
    {
        return array_merge([
            'exception_type' => 'RuntimeException',
            'message' => 'User not found',
            'file' => 'app/Services/UserService.php',
            'line' => 42,
            'stack_trace' => "#0 app/Services/UserService.php(42)\n#1 app/Http/Controllers/UserController.php(20)",
            'status_code' => 500,
            'method' => 'GET',
            'path' => '/users/123',
            'environment' => 'production',
            'laravel_version' => '11.0.0',
            'php_version' => '8.3.0',
            'agent_version' => '1.0.0',
            'occurred_at' => now()->toIso8601String(),
        ], $overrides);
    }

    public function test_valid_token_can_send_exception(): void
    {
        $response = $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
        ]));

        $response->assertStatus(201)
            ->assertJson([
                'received' => true,
            ])
            ->assertJsonStructure([
                'received',
                'group_uuid',
                'occurrence_uuid',
            ]);

        $this->assertDatabaseHas('exception_groups', [
            'project_id' => $this->project->id,
            'exception_type' => 'RuntimeException',
        ]);

        $this->assertDatabaseHas('exception_occurrences', [
            'project_id' => $this->project->id,
            'message' => 'User not found',
            'status_code' => 500,
        ]);
    }

    public function test_invalid_token_rejected(): void
    {
        $response = $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => 'invalid_token',
        ]));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid token format.']);
    }

    public function test_revoked_token_rejected(): void
    {
        $rawToken = $this->rawToken;
        $this->token->revoke();

        $response = $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $rawToken,
        ]));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token has been revoked.']);
    }

    public function test_missing_token_rejected(): void
    {
        $payload = $this->makePayload();
        unset($payload['token']);

        $response = $this->postJson('/api/agent/exceptions', $payload);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token not provided.']);
    }

    public function test_missing_required_fields_rejected(): void
    {
        $response = $this->postJson('/api/agent/exceptions', [
            'token' => $this->rawToken,
            // Missing exception_type, message, file, line, stack_trace
        ]);

        $response->assertStatus(422);
    }

    public function test_exception_grouping_creates_one_group(): void
    {
        // Send same exception twice
        $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
            'occurred_at' => now()->subMinutes(5)->toIso8601String(),
        ]))->assertStatus(201);

        $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
        ]))->assertStatus(201);

        // Should only have one group
        $this->assertEquals(1, \App\Models\ExceptionGroup::count());

        // But two occurrences
        $this->assertEquals(2, \App\Models\ExceptionOccurrence::count());

        // Group occurrence count should be 2
        $group = \App\Models\ExceptionGroup::first();
        $this->assertEquals(2, $group->occurrence_count);
    }

    public function test_exception_group_updates_first_and_last_seen(): void
    {
        $oldTime = now()->subHours(2);
        $newTime = now();

        $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
            'occurred_at' => $oldTime->toIso8601String(),
        ]))->assertStatus(201);

        $group = \App\Models\ExceptionGroup::first();

        $this->assertEquals(
            $oldTime->format('Y-m-d H:i'),
            $group->first_seen_at->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $oldTime->format('Y-m-d H:i'),
            $group->last_seen_at->format('Y-m-d H:i')
        );

        sleep(1); // Ensure time difference

        $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
            'occurred_at' => $newTime->toIso8601String(),
        ]))->assertStatus(201);

        $group->refresh();

        $this->assertEquals(
            $oldTime->format('Y-m-d H:i'),
            $group->first_seen_at->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $newTime->format('Y-m-d H:i'),
            $group->last_seen_at->format('Y-m-d H:i')
        );
    }

    public function test_resolved_group_reopens_on_new_exception(): void
    {
        // Create initial exception
        $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
        ]))->assertStatus(201);

        $group = \App\Models\ExceptionGroup::first();
        $this->assertEquals('open', $group->status);

        // Mark as resolved
        $group->markAsResolved();
        $this->assertEquals('resolved', $group->status);

        // Send another exception
        $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
        ]))->assertStatus(201);

        // Group should be reopened
        $group->refresh();
        $this->assertEquals('open', $group->status);
    }

    public function test_exception_stored_under_correct_project(): void
    {
        $otherOrganization = Organization::factory()->create();
        $otherProject = Project::factory()->create([
            'organization_id' => $otherOrganization->id,
        ]);
        $otherTokenData = (new GenerateAgentToken)->execute($otherProject);

        $payload = $this->makePayload(['token' => $otherTokenData['token']]);

        $response = $this->postJson('/api/agent/exceptions', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('exception_occurrences', [
            'project_id' => $otherProject->id,
        ]);

        $this->assertDatabaseMissing('exception_occurrences', [
            'project_id' => $this->project->id,
        ]);
    }

    public function test_exception_can_be_correlated_with_request(): void
    {
        // First create a request event
        $requestId = 'req_' . Str::random(20);

        $this->project->requestEvents()->create([
            'request_id' => $requestId,
            'method' => 'GET',
            'path' => '/users/123',
            'url' => 'http://localhost/users/123',
            'status_code' => 500,
            'duration_ms' => 150,
            'memory_bytes' => 1024 * 1024 * 10,
            'requested_at' => now(),
        ]);

        // Now send exception with same request ID
        $response = $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
            'request_id' => $requestId,
        ]));

        $response->assertStatus(201);

        $this->assertDatabaseHas('exception_occurrences', [
            'request_id' => $requestId,
        ]);
    }

    public function test_exception_without_request_id_is_allowed(): void
    {
        $response = $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
        ]));

        $response->assertStatus(201);

        $occurrence = \App\Models\ExceptionOccurrence::first();
        $this->assertNull($occurrence->request_id);
    }

    public function test_sanitized_stack_trace_removes_passwords(): void
    {
        $stackTraceWithPassword = <<<TRACE
#0 /var/www/html/app/Services/UserService.php(42)
UserService->updatePassword(): void
password='secret123'
TRACE;

        $response = $this->postJson('/api/agent/exceptions', $this->makePayload([
            'token' => $this->rawToken,
            'stack_trace' => $stackTraceWithPassword,
        ]));

        $response->assertStatus(201);

        $occurrence = \App\Models\ExceptionOccurrence::first();

        // Password should be redacted
        $this->assertStringContainsString('[REDACTED]', $occurrence->stack_trace);
        $this->assertStringNotContainsString('secret123', $occurrence->stack_trace);
    }
}
