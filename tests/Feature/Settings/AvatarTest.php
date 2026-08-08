<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('avatars');
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_user_can_upload_avatar(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($this->user)
            ->post(route('settings.avatar.upload'), [
                'avatar' => $file,
            ]);

        $response->assertRedirect();
        $this->user->refresh();
        $this->assertNotNull($this->user->avatar_path);
        Storage::disk('avatars')->assertExists($this->user->avatar_path);
    }

    public function test_user_can_remove_avatar(): void
    {
        $this->user->update(['avatar_path' => 'test-avatar.jpg']);
        Storage::disk('avatars')->put('test-avatar.jpg', 'fake-image-content');

        $response = $this->actingAs($this->user)
            ->delete(route('settings.avatar.remove'));

        $response->assertRedirect();
        $this->user->refresh();
        $this->assertNull($this->user->avatar_path);
        Storage::disk('avatars')->assertMissing('test-avatar.jpg');
    }

    public function test_upload_rejects_invalid_file_type(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user)
            ->post(route('settings.avatar.upload'), [
                'avatar' => $file,
            ]);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_upload_rejects_file_larger_than_2mb(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg')->size(3000);

        $response = $this->actingAs($this->user)
            ->post(route('settings.avatar.upload'), [
                'avatar' => $file,
            ]);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_uploading_new_avatar_replaces_old_one(): void
    {
        $this->user->update(['avatar_path' => 'old-avatar.jpg']);
        Storage::disk('avatars')->put('old-avatar.jpg', 'old-image-content');

        $newFile = UploadedFile::fake()->image('new-avatar.jpg', 200, 200);

        $this->actingAs($this->user)
            ->post(route('settings.avatar.upload'), [
                'avatar' => $newFile,
            ]);

        Storage::disk('avatars')->assertMissing('old-avatar.jpg');
        $this->user->refresh();
        $this->assertNotEquals('old-avatar.jpg', $this->user->avatar_path);
    }

    public function test_unauthenticated_user_cannot_upload_avatar(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->post(route('settings.avatar.upload'), [
            'avatar' => $file,
        ]);

        $response->assertRedirect('/login');
    }
}
