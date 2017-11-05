<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_a_profile()
    {
        $user = create('App\User');

        $this->get(route('user.show', $user))
            ->assertSee(e($user->name));
    }

    /** @test */
    public function profiles_display_all_threads_created_by_the_associated_user()
    {
        $this->signIn();
        $threads = create('App\Thread', ['user_id' => auth()->id()], 3);

        $this->get(route('user.show', auth()->user()))
            ->assertSee($threads[0]->title)
            ->assertSee($threads[1]->title)
            ->assertSee($threads[2]->title);
    }
}
