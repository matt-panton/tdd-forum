<?php

namespace Tests\Unit;

use Tests\TestCase;
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
}
