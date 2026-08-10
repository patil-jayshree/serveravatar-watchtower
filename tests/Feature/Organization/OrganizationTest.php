<?php

namespace Tests\Feature\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTest extends TestCase
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

    /**
     * @group view
     */
    public function test_authenticated_user_can_view_organizations_list(): void
    {
        $this->markTestSkipped('Skipped: tempnam issue in test environment when rendering Blade views');

        $response = $this->actingAs($this->user)
            ->get(route('organizations.index'));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_organization(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('organizations.store'), [
                'name' => 'My Test Organization',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('organizations', [
            'name' => 'My Test Organization',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_creator_becomes_owner(): void
    {
        $this->actingAs($this->user)
            ->post(route('organizations.store'), [
                'name' => 'My Test Organization',
            ]);

        $organization = Organization::where('name', 'My Test Organization')->first();

        $this->assertNotNull($organization);
        $this->assertEquals($this->user->id, $organization->user_id);
    }

    public function test_user_can_own_multiple_organizations(): void
    {
        $this->actingAs($this->user)
            ->post(route('organizations.store'), ['name' => 'Organization 1']);

        $this->actingAs($this->user)
            ->post(route('organizations.store'), ['name' => 'Organization 2']);

        $this->assertEquals(2, $this->user->organizations()->count());
    }

    public function test_owner_can_update_organization(): void
    {
        $organization = Organization::create([
            'name' => 'Original Name',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('organizations.settings.update', $organization), [
                'name' => 'Updated Name',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_non_owner_cannot_update_organization(): void
    {
        $otherUser = User::factory()->create();

        $organization = Organization::create([
            'name' => 'Original Name',
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('organizations.settings.update', $organization), [
                'name' => 'Hacked Name',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Original Name',
        ]);
    }

    public function test_owner_can_delete_organization(): void
    {
        $organization = Organization::create([
            'name' => 'Test Organization',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('organizations.settings.update', $organization), [
                'delete' => '1',
            ]);

        $response->assertRedirect(route('organizations.index'));
        $this->assertDatabaseMissing('organizations', [
            'id' => $organization->id,
        ]);
    }

    public function test_non_owner_cannot_delete_organization(): void
    {
        $otherUser = User::factory()->create();

        $organization = Organization::create([
            'name' => 'Test Organization',
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('organizations.settings.update', $organization), [
                'delete' => '1',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_organizations(): void
    {
        $response = $this->get(route('organizations.index'));

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_organization_they_dont_own(): void
    {
        $otherUser = User::factory()->create();
        $organization = Organization::create([
            'name' => 'Other Org',
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('organizations.show', $organization));

        $response->assertStatus(403);
    }
}
