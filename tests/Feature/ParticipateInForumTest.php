<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_may_not_participate_in_forum_threads()
    {
        $thread = create('App\Thread');

        $this->withExceptionHandling()
            ->post($thread->path().'/replies', [])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply');

        $this->post($thread->path().'/replies', $reply->toArray());

        $this->get($thread->path())
            ->assertSee($reply['body']);
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->signIn();
        $thread = create('App\Thread');

        $reply = make('App\Reply', ['body' => null]);

        $this->withExceptionHandling()
            ->post($thread->path().'/replies', $reply->toArray())
            ->assertSessionHasErrors('body');

    }
}
