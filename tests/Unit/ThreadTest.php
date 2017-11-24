<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_thread_has_a_path()
    {
        $this->assertEquals(
            url("threads/{$this->thread->channel->slug}/{$this->thread->slug}"),
            $this->thread->path()
        );
    }

    /** @test */
    public function a_thread_can_have_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function a_thread_belongs_to_a_user()
    {
        $this->assertInstanceOf('App\User', $this->thread->user);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $user = create('App\User');

        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => $user->id,
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function a_thread_nofifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();
        $user = create('App\User');
        $subscribedUser = create('App\User');

        $this->thread
            ->subscribe($subscribedUser)
            ->addReply([
                'body' => 'foobar',
                'user_id' => $user->id,
            ]);

        Notification::assertSentTo($subscribedUser, ThreadWasUpdated::class);
    }

    /** @test */
    public function a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        $user = create('App\User');

        $this->thread->subscribe($user);

        $this->assertEquals(
            1,
            $this->thread->subscriptions()->where('user_id', $user->id)->count()
        );
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        $user = create('App\User');

        $this->thread->subscribe($user);

        $this->thread->unsubscribe($user);

        $this->assertEquals(
            0,
            $this->thread->subscriptions()->where('user_id', $user->id)->count()
        );
    }

    /** @test */
    public function it_knows_whether_the_authenticated_user_is_subscribed_to_it()
    {
        $this->signIn();

        $this->assertFalse($this->thread->isSubscribedTo);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);
    }

    /** @test */
    public function a_thread_can_check_whether_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();
        $user = auth()->user();

        $thread = create('App\Thread');

        $this->assertTrue($thread->hasUpdatesFor($user));

        // Simulate that the user visited the thread page.
        $user->read($thread);
        
        $this->assertFalse($thread->hasUpdatesFor($user));
    }
}
