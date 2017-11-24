<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscribeToThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_cannot_subscribe_to_threads()
    {
        $thread = create('App\Thread');

        $this->withExceptionHandling()
            ->post(route('subscription.store', [$thread->channel, $thread]))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_can_subscribe_to_threads()
    {
        $this->signIn();
        $thread = create('App\Thread');

        $this->post(route('subscription.store', [$thread->channel, $thread]));

        $this->assertCount(1, $thread->subscriptions);
    }

    /** @test */
    public function an_authenticated_user_can_unsubscribe_from_a_threads()
    {
        $this->signIn();
        $thread = create('App\Thread');

        $thread->subscribe();

        $this->delete(route('subscription.destroy', [$thread->channel, $thread]));

        $this->assertDatabaseMissing('thread_subscriptions', [
            'user_id' => auth()->id(),
            'thread_id' => $thread->id,
        ]);
    }
}
