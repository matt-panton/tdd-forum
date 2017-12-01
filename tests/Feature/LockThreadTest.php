<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LockThreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_non_administrator_cannot_lock_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->withExceptionHandling()
            ->json('post', route('locked-thread.store', $thread))
            ->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function an_administartor_can_lock_a_thread()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread');

        $this->withExceptionHandling()
            ->json('post', route('locked-thread.store', $thread))
            ->assertStatus(200);

        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    public function an_non_administartor_cannot_unlock_a_thread()
    {
        $this->signIn();

        $thread = create('App\Thread', ['locked' => true]);
        
        $this->assertTrue($thread->fresh()->locked);

        $this->withExceptionHandling()
            ->json('delete', route('locked-thread.destroy', $thread))
            ->assertStatus(403);

        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    public function an_administartor_can_unlock_a_thread()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread', ['locked' => true]);
        
        $this->assertTrue($thread->fresh()->locked);

        $this->withExceptionHandling()
            ->json('delete', route('locked-thread.destroy', $thread))
            ->assertStatus(200);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function a_locked_thread_cannot_be_replied_to()
    {
        $thread = create('App\Thread', ['locked' => true]);

        $this->withExceptionHandling()->signIn();

        $this->json('post', $thread->path().'/replies', [
            'body' => 'Here is my reply.'
        ])
        ->assertStatus(422);

        $this->assertDatabaseMissing('replies', ['body' => 'Here is my reply.']);
    }
}
