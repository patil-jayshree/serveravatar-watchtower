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

    public function test_authenticated_user_can_view_organizations_list(): void
    {
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
            'owner_id' => $this->user->id,
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
        $this->assertEquals($this->user->id, $organization->owner_id);

        $membership = $organization->memberships()->where('user_id', $this->user->id)->first();
        $this->assertNotNull($membership);
        $this->assertEquals('owner', $membership->role->value);
    }

    public function test_user_can_belong_to_multiple_organizations(): void
    {
        $this->actingAs($this->user)
            ->post(route('organizations.store'), ['name' => 'Organization 1']);

        $this->actingAs($this->user)
            ->post(route('organizations.store'), ['name' => 'Organization 2']);

        $this->assertEquals(2, $this->user->memberOf()->count());
    }

    public function test_organization_can_be_updated_by_admin(): void
    {
        $organization = Organization::create([
            'name' => 'Original Name',
            'owner_id' => $this->user->id,
        ]);

        $organization->memberships()->create([
            'user_id' => $this->user->id,
            'role' => 'admin',
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

    public function test_member_cannot_update_organization(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $organization = Organization::create([
            'name' => 'Original Name',
            'owner_id' => $owner->id,
        ]);

        $organization->memberships()->create([
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $organization->memberships()->create([
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $response = $this->actingAs($member)
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
            'owner_id' => $this->user->id,
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
        $owner = User::factory()->create();
        $admin = User::factory()->create();

        $organization = Organization::create([
            'name' => 'Test Organization',
            'owner_id' => $owner->id,
        ]);

        $organization->memberships()->create([
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $organization->memberships()->create([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
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

    public function test_user_cannot_access_organization_they_dont_belong_to(): void
    {
        $otherUser = User::factory()->create();
        $organization = Organization::create([
            'name' => 'Other Org',
            'owner_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('organizations.show', $organization));

        $response->assertStatus(403);
    }
}
