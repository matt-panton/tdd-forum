<?php

namespace Tests\Feature;

use App\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_create_new_forum_threads()
    {
        $this->withExceptionHandling();

        $this->get(route('thread.create'))
            ->assertRedirect(route('login'));

        $this->post(route('thread.store'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $user = factory('App\User')->states('unconfirmed')->create();
        $this->signIn($user);

        $thread = factory('App\Thread')->make();

        return $this->post(route('thread.store'), $thread->toArray())
            ->assertRedirect(route('thread.index'))
            ->assertSessionHas('flash');
    }

    /** @test */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
        $this->signIn();

        $thread = make('App\Thread');

        $response = $this->post(route('thread.store'), $thread->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /** @test */
    public function a_thead_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thead_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_creates_a_unique_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Foo Title', 'slug' => 'foo-title']);

        $this->post(route('thread.store'), $thread->toArray());

        $this->assertDatabaseHas('threads', ['slug' => 'foo-title-2']);
    }

    /** @test */
    public function a_thead_requires_a_valid_channel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function a_thread_can_be_deleted_by_authorized_user()
    {
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());
            
        $response->assertStatus(200);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertCount(0, Activity::all());
    }

    /** @test */
    public function an_unauthorized_user_cannot_delete_a_thread()
    {
        $this->signIn();
        $thread = create('App\Thread');

        $this->withExceptionHandling()
            ->delete($thread->path())
            ->assertStatus(403);
    }

    protected function publishThread($attributes = [])
    {
        $this->signIn();

        $thread = factory('App\Thread')->make($attributes);

        return $this->withExceptionHandling()
            ->post(route('thread.store'), $thread->toArray());
    }
}
