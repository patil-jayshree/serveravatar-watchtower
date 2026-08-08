<?php

namespace Tests\Feature\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationSwitchingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Organization $org1;
    protected Organization $org2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->org1 = Organization::create([
            'name' => 'Organization 1',
            'owner_id' => $this->user->id,
        ]);

        $this->org2 = Organization::create([
            'name' => 'Organization 2',
            'owner_id' => $this->user->id,
        ]);

        $this->user->memberOf()->create([
            'organization_id' => $this->org1->id,
            'role' => 'owner',
        ]);

        $this->user->memberOf()->create([
            'organization_id' => $this->org2->id,
            'role' => 'owner',
        ]);
    }

    public function test_user_can_switch_to_organization_they_belong_to(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('organizations.switch', $this->org1));

        $response->assertRedirect();
        $this->assertEquals($this->org1->id, session('selected_organization_id'));
    }

    public function test_user_cannot_switch_to_organization_they_dont_belong_to(): void
    {
        $otherOrg = Organization::create([
            'name' => 'Other Organization',
            'owner_id' => User::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('organizations.switch', $otherOrg));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_selected_organization_persists(): void
    {
        $this->actingAs($this->user)
            ->post(route('organizations.switch', $this->org1));

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee($this->org1->name);
    }

    public function test_default_organization_is_selected_on_login(): void
    {
        // User logs in (switch to first org)
        $this->actingAs($this->user)
            ->post(route('organizations.switch', $this->org1));

        // Logout
        $this->post(route('logout'));

        // Login again
        $this->actingAs($this->user)
            ->get(route('dashboard'));

        // The user should see org1 as the selected organization
        $this->assertEquals($this->org1->id, session('selected_organization_id'));
    }
}
