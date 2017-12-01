<?php

namespace Tests\Unit;

use App\User;
use App\Reply;
use Carbon\Carbon;
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
            url("threads/{$thread->channel->slug}/{$thread->slug}") . "#reply-{$reply->id}",
            $reply->path()
        );
    }

    /** @test */
    public function it_knows_if_it_was_just_published()
    {
        $reply = create('App\Reply');
        
        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subDay();
        
        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function it_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = new Reply([
            'body' => '@JaneDoe wants to talk to @JohnDoe'
        ]);

        $this->assertEquals(['JaneDoe', 'JohnDoe'], $reply->mentionedUsers());
    }

    /** @test */
    public function it_wraps_mentioned_usernames_in_the_body_within_anchor_tags()
    {
        $reply = new Reply([
            'body' => 'Hello @JaneDoe and @SamSmith.'
        ]);

        $this->assertEquals(
            'Hello <a href="'. route('user.show', 'JaneDoe') .'">@JaneDoe</a> and <a href="'. route('user.show', 'SamSmith') .'">@SamSmith</a>.',
            $reply->formatted_body
        );
    }

    /** @test */
    public function it_knows_if_it_is_the_best_reply()
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->is_best);

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->is_best);
    }
}
