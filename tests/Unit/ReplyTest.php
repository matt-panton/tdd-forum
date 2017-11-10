<?php

namespace Tests\Unit;

use App\User;
use App\Reply;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_has_an_owner()
    {
        $reply = factory(Reply::class)->create();

        $this->assertInstanceOf(User::class, $reply->user);
    }

    /** @test */
    public function a_reply_can_be_favourited()
    {
        $user = create('App\User');
        $reply = create('App\Reply');

        $reply->favourite($user);
        
        $this->assertCount(1, $reply->favourites);
        $this->assertEquals($user->id, $reply->favourites()->first()->user->id);
    }

    /** @test */
    public function a_reply_can_make_a_string_path()
    {
        $thread = create('App\Thread');
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $this->assertEquals(
            url("threads/{$thread->channel->slug}/{$thread->id}") . "#reply-{$reply->id}",
            $reply->path()
        );
    }
}
