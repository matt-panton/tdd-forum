<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_channel_can_have_many_threads()
    {
        $channel = create('App\Channel');
        $threads = factory('App\Thread', 2)->create(['channel_id' => $channel->id]);

        $this->assertCount(2, $channel->threads);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $channel->threads);
    }
}
