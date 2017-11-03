<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavouritesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_favourite_anything()
    {
        $reply = create('App\Reply');

        $this->withExceptionHandling()
            ->post(route('reply.favourite', $reply))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_can_favourite_any_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');

        $this->post(route('reply.favourite', $reply));

        $this->assertCount(1, $reply->favourites);
    }

    /** @test */
    public function an_authenticated_user_can_only_favourite_a_reply_once()
    {
        $this->signIn();
        $reply = create('App\Reply');

        try {
            $this->post(route('reply.favourite', $reply));
            $this->post(route('reply.favourite', $reply));
        } catch (\Exception $e) {
            $this->fail('Did not expect to insert the same record set twice.');
        }

        $this->assertCount(1, $reply->favourites);
    }
}
