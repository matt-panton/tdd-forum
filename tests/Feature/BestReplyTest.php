<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BestReplyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_thread_creator_may_mark_any_answer_as_best_reply()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);
        
        $this->assertFalse($replies[1]->is_best);

        $this->json('post', route('best-reply.store', $replies[1]));

        $this->assertTrue($replies[1]->fresh()->is_best);
    }

    /** @test */
    public function only_authorized_users_may_mark_reply_as_best()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->signIn(create('App\User'));

        $this->withExceptionHandling()
            ->json('post', route('best-reply.store', $replies[1]))
            ->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->is_best);
    }

    /** @test */
    public function if_the_best_reply_is_deleted_the_thread_is_updated_to_reflect_that()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->assertTrue($reply->fresh()->is_best);

        $this->json('delete', route('reply.destroy', $reply));
        
        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}
