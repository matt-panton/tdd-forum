<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_authenticated_users_can_add_avatars()
    {
        $user = create('App\User');

        $this->withExceptionHandling()
            ->json('post', route('avatar.store', $user))
            ->assertStatus(401);
    }

    /** @test */
    public function only_authorized_users_can_add_avatars()
    {
        $this->withExceptionHandling()->signIn();
        $user = create('App\User');

        $this->json('post', route('avatar.store', $user), [
                'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function a_valid_avatar_must_be_provided()
    {
        $this->withExceptionHandling()->signIn();

        $this->json('post', route('avatar.store', auth()->user()), [
            'avatar' => 'not-an-image'
        ])
        ->assertStatus(422);
    }

    /** @test */
    public function old_avatar_is_removed_from_disk_when_replaced()
    {
        $this->signIn();

        Storage::fake('public');

        $this->json('post', route('avatar.store', auth()->user()), [
            'avatar' => $file1 = UploadedFile::fake()->image('avatar.jpg')
        ]);

        Storage::disk('public')->assertExists("avatars/{$file1->hashName()}");

        $this->json('post', route('avatar.store', auth()->user()), [
            'avatar' => $file2 = UploadedFile::fake()->image('avatar.jpg')
        ]);
        
        Storage::disk('public')->assertMissing("avatars/{$file1->hashName()}");
        Storage::disk('public')->assertExists("avatars/{$file2->hashName()}");
    }

    /** @test */
    public function an_authenticated_user_may_add_an_avatar_to_their_profile()
    {
        $this->withExceptionHandling()->signIn();

        Storage::fake('public');

        $this->json('post', route('avatar.store', auth()->user()), [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertEquals("avatars/{$file->hashName()}", auth()->user()->fresh()->avatar_path);

        Storage::disk('public')->assertExists("avatars/{$file->hashName()}");
    }
}
