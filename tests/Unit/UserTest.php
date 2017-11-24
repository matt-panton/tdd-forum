<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = create('App\User');
    }

    /** @test */
    public function a_user_can_determine_whether_is_has_favourited_a_given_model()
    {
        $replyThatHasBeenFavourited = create('App\Reply');
        $replyThatHasNotBeenFavourited = create('App\Reply');

        $replyThatHasBeenFavourited->favourite($this->user);

        $this->assertTrue($this->user->hasFavourited($replyThatHasBeenFavourited));
        $this->assertFalse($this->user->hasFavourited($replyThatHasNotBeenFavourited));
    }

    /** @test */
    public function a_user_can_determine_whether_it_owns_a_given_model()
    {
        $threadCreatedByUser = create('App\Thread', ['user_id'=>$this->user->id]);
        $threadNotCreatedByUser = create('App\Thread');

        $this->assertTrue($this->user->owns($threadCreatedByUser));
        $this->assertFalse($this->user->owns($threadNotCreatedByUser));
    }

    /** @test */
    public function a_user_can_fetch_their_must_recent_reply()
    {
        $reply = create('App\Reply', ['user_id' => $this->user->id]);

        $this->assertEquals($reply->id, $this->user->lastReply->id);
    }

    /** @test */
    public function a_user_can_determine_their_avatar_path()
    {
        $this->assertEquals(asset('images/avatar-default.png'), $this->user->avatar);

        $this->user->avatar_path = 'avatars/me.jpg';
        
        $this->assertEquals(asset('storage/avatars/me.jpg'), $this->user->avatar);
    }

    /** @test */
    public function a_user_can_remove_their_existing_avatar_from_disk()
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.jpg');
        $avatar->store('avatars', 'public');
        $avatarPath = 'avatars/'.$avatar->hashName();

        $this->user->update(['avatar_path' => $avatarPath]);

        Storage::disk('public')->assertExists($avatarPath);

        $this->user->removeExistingAvatar();

        $this->assertNull($this->user->fresh()->avatar_path);
        Storage::disk('public')->assertMissing($avatarPath);
    }
}
