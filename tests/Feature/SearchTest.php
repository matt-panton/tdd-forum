<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        config(['scout.driver' => 'algolia']);
    }

    public function tearDown()
    {
        Artisan::call('scout:flush', ['model' => 'App\Thread']);
    }

    /** @test */
    public function a_user_can_search_threads()
    {
        $search = 'foobar';

        create('App\Thread', ['title' => 'Here is the title'], 2);
        create('App\Thread', ['body' => "A thread with the {$search} term."], 2);

        do {
            sleep(0.25);
            $results = $this->json('get', route('thread.search', ['q' => $search]))->json();
        } while (empty($results['data']));

        $this->assertCount(2, $results['data']);
    }
}
