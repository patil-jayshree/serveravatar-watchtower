<?php

namespace Tests\Feature\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationMembershipTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $admin;
    protected User $member;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->member = User::factory()->create();

        $this->organization = Organization::create([
            'name' => 'Test Organization',
            'owner_id' => $this->owner->id,
        ]);

        $this->organization->memberships()->create([
            'user_id' => $this->owner->id,
            'role' => 'owner',
        ]);

        $this->organization->memberships()->create([
            'user_id' => $this->admin->id,
            'role' => 'admin',
        ]);

        $this->organization->memberships()->create([
            'user_id' => $this->member->id,
            'role' => 'member',
        ]);
    }

    public function test_admin_can_add_member(): void
    {
        $newUser = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('organizations.members.store', $this->organization), [
                'email' => $newUser->email,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('organization_memberships', [
            'organization_id' => $this->organization->id,
            'user_id' => $newUser->id,
            'role' => 'member',
        ]);
    }

    public function test_member_cannot_add_member(): void
    {
        $newUser = User::factory()->create();

        $response = $this->actingAs($this->member)
            ->post(route('organizations.members.store', $this->organization), [
                'email' => $newUser->email,
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_member_role(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('organizations.members.update', [$this->organization, $this->member]), [
                'role' => 'admin',
            ]);

        $response->assertRedirect();
        $this->assertEquals('admin', $this->member->fresh()->memberOf()->where('organization_id', $this->organization->id)->first()->role->value);
    }

    public function test_cannot_change_owner_role(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('organizations.members.update', [$this->organization, $this->owner]), [
                'role' => 'member',
            ]);

        $response->assertSessionHasErrors();
    }

    public function test_admin_can_remove_member(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('organizations.members.destroy', [$this->organization, $this->member]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('organization_memberships', [
            'organization_id' => $this->organization->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_cannot_remove_owner(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('organizations.members.destroy', [$this->organization, $this->owner]));

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('organization_memberships', [
            'organization_id' => $this->organization->id,
            'user_id' => $this->owner->id,
        ]);
    }

    public function test_member_cannot_remove_member(): void
    {
        $otherMember = User::factory()->create();
        $this->organization->memberships()->create([
            'user_id' => $otherMember->id,
            'role' => 'member',
        ]);

        $response = $this->actingAs($this->member)
            ->delete(route('organizations.members.destroy', [$this->organization, $otherMember]));

        $response->assertStatus(403);
    }

    public function test_cannot_add_duplicate_member(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('organizations.members.store', $this->organization), [
                'email' => $this->member->email,
            ]);

        $response->assertSessionHasErrors('email');
    }
}
