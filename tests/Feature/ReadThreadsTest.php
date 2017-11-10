<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReadThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }
    
    /** @test */
    public function a_user_can_browse_all_threads()
    {
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_view_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_tag()
    {
        $channel = create('App\Channel');

        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get(route('thread.index', $channel))
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_given_username()
    {
        $this->signIn(create('App\User', ['name'=>'JohnDoe']));
        
        $threadByJohn = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJohn = create('App\Thread');

        $this->get(route('thread.index').'?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    public function by_query_is_ignored_if_username_does_not_exists()
    {
        $user = create('App\User', ['name' => 'gary']);
        $threads = factory('App\Thread', 4)->create(['user_id' => $user->id]);

        $this->get(route('thread.index').'?by=doesNotExist')
            ->assertSee($threads->first()->title);
    }

    /** @test */
    public function a_user_can_filter_therads_by_popularity()
    {
        $threadWithNoReplies = $this->thread;
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);
        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

        $response = $this->getJson(route('thread.index').'?popular=1')->json();

        $this->assertEquals([3, 2, 0],array_column($response, 'replies_count'));
    }

    /** @test */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $threadWithNoReplies = $this->thread;
        $threadWithReply = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithReply->id]);

        $response = $this->getJson(route('thread.index').'?unanswered=1')->json();

        $this->assertCount(1, $response);
        $this->assertEquals($threadWithNoReplies->body, $response[0]['body']);
    }

    /** @test */
    public function a_user_can_request_replies_for_a_given_thread()
    {
        $thread = create('App\Thread');
        $replies = create('App\Reply', ['thread_id' => $thread->id], 3);

        $response = $this->json('GET', route('reply.index', [$thread->channel, $thread]))->json();

        $this->assertCount(3, $response['data']);
    }
}
