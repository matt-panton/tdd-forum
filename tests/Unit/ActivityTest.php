<?php

namespace Tests\Unit;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_records_activity_when_a_thread_is_created()
    {
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread',
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $thread->id);
    }

    /** @test */
    public function it_records_activity_when_a_reply_is_created()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->assertCount(2, Activity::all());
    }

    /** @test */
    public function it_fetches_an_activity_feed_for_any_user()
    {
        $this->signIn();

        create('App\Thread', ['user_id' => auth()->id()], 2);
        auth()->user()->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        $feed = Activity::feed(auth()->user());

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('d-m-Y'))
        );
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('d-m-Y'))
        );
    }
}
