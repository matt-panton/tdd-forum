<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $john = create('App\User', ['name' => 'JohnDoe']);
        $this->signIn($john);
        $sam = create('App\User', ['name' => 'SamSmith']);

        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => 'Hey @SamSmith look at this.',
        ]);

        $this->json('post', $thread->path().'/replies', $reply->toArray());

        $this->assertCount(1, $sam->notifications);
    }

    /** @test */
    public function it_can_fetch_mentioned_users_starting_with_a_given_string_of_characters()
    {
        create('App\User', ['name'=>'johndoe']);
        create('App\User', ['name'=>'janedoe']);
        create('App\User', ['name'=>'johnsmith']);

        $response = $this->json('get', '/api/users', ['name' => 'john'])->json();

        $this->assertCount(2, $response);
    }
}
