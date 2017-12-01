<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->signIn();
        $this->withExceptionHandling();
    }

    /** @test */
    public function an_authorized_user_can_update_a_thread()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), ['title' => 'New title', 'body' => 'New body'])
            ->assertRedirect($thread->fresh()->path());

        $this->assertEquals('New title', $thread->fresh()->title);
        $this->assertEquals('New body', $thread->fresh()->body);
    }

    /** @test */
    public function an_unauthorized_user_cannot_update_a_thread()
    {
        $thread = create('App\Thread');

        $this->patch($thread->path(), ['title' => 'New title', 'body' => 'New body'])
            ->assertStatus(403);
    }

    /** @test */
    public function a_thread_requires_a_title_and_body_to_be_updated()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), ['title' => 'New title'])
            ->assertSessionHasErrors('body');

        $this->patch($thread->path(), ['body' => 'New body'])
            ->assertSessionHasErrors('title');
    }
}
