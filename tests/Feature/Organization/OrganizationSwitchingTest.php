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
            'user_id' => $this->user->id,
        ]);

        $this->org2 = Organization::create([
            'name' => 'Organization 2',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_switch_to_their_organization(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('organizations.switch', $this->org1));

        $response->assertRedirect();
        $this->assertEquals($this->org1->id, session('selected_organization_id'));
    }

    public function test_user_cannot_switch_to_organization_they_dont_own(): void
    {
        $otherUser = User::factory()->create();
        $otherOrg = Organization::create([
            'name' => 'Other Organization',
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('organizations.switch', $otherOrg));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * @group view
     */
    public function test_selected_organization_persists(): void
    {
        $this->markTestSkipped('Skipped: tempnam issue in test environment when rendering Blade views');

        $this->actingAs($this->user)
            ->post(route('organizations.switch', $this->org1));

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee($this->org1->name);
    }
}
