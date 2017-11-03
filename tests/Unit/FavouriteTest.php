<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavouriteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_favourite_belongs_to_a_user()
    {
        $user = create('App\User');
        $favourite = create('App\Favourite', ['user_id' => $user->id]);

        $this->assertInstanceOf('App\User', $favourite->user);
    }
}
