<?php

namespace Tests\Feature;

use Carbon\Carbon;
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

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
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

    /** @test */
    public function unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();
        $reply = create('App\Reply');

        $this->delete(route('reply.destroy', $reply))
            ->assertRedirect(route('login'));
        
        $this->signIn()
            ->delete(route('reply.destroy', $reply))
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->json('DELETE', route('reply.destroy', $reply))
            ->assertStatus(200);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();
        $reply = create('App\Reply');

        $this->json('PATCH', route('reply.update', $reply))
            ->assertStatus(401);

        $this->signIn()
            ->json('PATCH', route('reply.update', $reply), ['body' => 'You have been changed.'])
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_edit_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', [
            'user_id' => auth()->id(),
            'created_at'  => Carbon::now()->subDay(),
        ]);

        $this->json('PATCH', route('reply.update', $reply), ['body' => 'You have been changed.'])
            ->assertStatus(200);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => 'You have been changed.',
        ]);
    }

    /** @test */
    public function replies_that_contain_span_may_not_be_created()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', [
            'body' => 'Yahoo Customer Support'
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->post($thread->path().'/replies', $reply->toArray());
    }

    /** @test */
    public function users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->signIn();
        $this->withExceptionHandling();
        $thread = create('App\Thread');

        $reply = make('App\Reply', ['body' => 'A simple reply.']);

        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertStatus(200);        

        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertStatus(403);
    }
}
