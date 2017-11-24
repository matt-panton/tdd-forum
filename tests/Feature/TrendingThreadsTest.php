<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Repositories\Trending;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrendingThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected $trending;

    public function setUp()
    {
        parent::setUp();

        $this->trending = new Trending;
        $this->trending->reset();
    }


    /** @test */
    public function it_incremenets_a_thread_score_each_time_it_is_read()
    {
        $this->assertEmpty($this->trending->get());

        $thread = create('App\Thread');

        $this->get($thread->path());

        $this->assertCount(1, $this->trending->get());
        $this->assertEquals($thread->title, $this->trending->get()[0]->title);
    }
}
