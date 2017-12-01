<?php

namespace Tests\Feature;

use App\Activity;
use Tests\TestCase;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        app()->singleton(Recaptcha::class, function () {
            $m = \Mockery::mock(Recaptcha::class);

            $m->shouldReceive('passes')->andReturn(true);

            return $m;
        });
    }

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
        $response = $this->publishThread(['title' => 'Some title', 'body' => 'some body']);

        $this->get($response->headers->get('Location'))
            ->assertSee('Some title')
            ->assertSee('some body');
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
    public function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);

        $this->publishThread(['g-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    public function a_thread_creates_a_unique_slug()
    {
        $this->signIn();

        create('App\Thread', [], 2);

        $thread = create('App\Thread', ['title' => 'Foo Title']);

        $this->assertEquals('foo-title', $thread['slug']);

        $thread = $this->json('post', route('thread.store'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals('foo-title-'.$thread['id'], $thread['slug']);
    }

    /** @test */
    public function a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Some Title 20']);

        $thread = $this->json('post', route('thread.store'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals('some-title-20-'.$thread['id'], $thread['slug']);
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
            ->post(route('thread.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }
}
