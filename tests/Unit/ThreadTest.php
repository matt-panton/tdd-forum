<?php

namespace Tests\Unit;

use Tests\TestCase;
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
    public function a_thread_can_make_a_string_path()
    {
        $this->assertEquals(
            url("threads/{$this->thread->channel->slug}/{$this->thread->id}"),
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
}
