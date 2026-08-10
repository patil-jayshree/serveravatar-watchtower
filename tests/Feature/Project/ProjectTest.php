<?php

namespace Tests\Feature\Project;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->organization = Organization::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * @group view
     */
    public function test_authenticated_user_can_view_projects_list(): void
    {
        $this->markTestSkipped('Skipped: tempnam issue in test environment when rendering Blade views');

        $response = $this->actingAs($this->user)
            ->get(route('organizations.projects.index', $this->organization));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_project(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('organizations.projects.store', $this->organization), [
                'name' => 'My Test Project',
                'description' => 'Test description',
                'framework' => 'laravel',
                'environment' => 'production',
                'status' => 'active',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'My Test Project',
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_project_belongs_to_organization(): void
    {
        $project = Project::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $this->assertEquals($this->organization->id, $project->organization->id);
    }

    public function test_required_fields_are_validated(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('organizations.projects.store', $this->organization), []);

        $response->assertSessionHasErrors(['name', 'framework', 'environment']);
    }

    public function test_invalid_framework_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('organizations.projects.store', $this->organization), [
                'name' => 'Test Project',
                'framework' => 'invalid_framework',
                'environment' => 'production',
            ]);

        $response->assertSessionHasErrors(['framework']);
    }

    public function test_invalid_environment_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('organizations.projects.store', $this->organization), [
                'name' => 'Test Project',
                'framework' => 'laravel',
                'environment' => 'invalid_environment',
            ]);

        $response->assertSessionHasErrors(['environment']);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('organizations.projects.store', $this->organization), [
                'name' => 'Test Project',
                'framework' => 'laravel',
                'environment' => 'production',
                'status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors(['status']);
    }

    /**
     * @group view
     */
    public function test_user_can_view_project_details(): void
    {
        $this->markTestSkipped('Skipped: tempnam issue in test environment when rendering Blade views');

        $project = Project::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('organizations.projects.show', [$this->organization, $project]));

        $response->assertStatus(200);
        $response->assertSee($project->name);
    }

    public function test_user_can_update_their_project(): void
    {
        $project = Project::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('organizations.projects.update', [$this->organization, $project]), [
                'name' => 'Updated Project Name',
                'framework' => 'laravel',
                'environment' => 'staging',
                'status' => 'inactive',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'environment' => 'staging',
            'status' => 'inactive',
        ]);
    }

    public function test_user_can_delete_their_project(): void
    {
        $project = Project::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('organizations.projects.destroy', [$this->organization, $project]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_unauthenticated_user_cannot_access_projects(): void
    {
        $response = $this->get(route('organizations.projects.index', $this->organization));

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_another_users_project(): void
    {
        $otherUser = User::factory()->create();
        $otherOrg = Organization::factory()->create(['user_id' => $otherUser->id]);
        $project = Project::factory()->create(['organization_id' => $otherOrg->id]);

        $response = $this->actingAs($this->user)
            ->get(route('organizations.projects.show', [$otherOrg, $project]));

        $response->assertStatus(403);
    }

    public function test_user_cannot_create_project_in_another_users_organization(): void
    {
        $otherUser = User::factory()->create();
        $otherOrg = Organization::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->post(route('organizations.projects.store', $otherOrg), [
                'name' => 'Unauthorized Project',
                'framework' => 'laravel',
                'environment' => 'production',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_modify_another_users_project(): void
    {
        $otherUser = User::factory()->create();
        $otherOrg = Organization::factory()->create(['user_id' => $otherUser->id]);
        $project = Project::factory()->create(['organization_id' => $otherOrg->id]);

        $response = $this->actingAs($this->user)
            ->put(route('organizations.projects.update', [$otherOrg, $project]), [
                'name' => 'Hacked Name',
                'framework' => 'laravel',
                'environment' => 'production',
                'status' => 'active',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_another_users_project(): void
    {
        $otherUser = User::factory()->create();
        $otherOrg = Organization::factory()->create(['user_id' => $otherUser->id]);
        $project = Project::factory()->create(['organization_id' => $otherOrg->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('organizations.projects.destroy', [$otherOrg, $project]));

        $response->assertStatus(403);
    }
}
