<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Mail\ConfirmYourEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();
        
        event(new Registered($user = create('App\User')));

        Mail::assertQueued(ConfirmYourEmail::class);
    }

    /** @test */
    public function a_token_is_required_when_confirming_email_address()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->get(route('register.confirm'));
    }

    /** @test */
    public function confirming_an_invalid_token()
    {
        $this->withExceptionHandling()
            ->get(route('register.confirm', ['token' => 'invalid']))
            ->assertRedirect('/');
    }

    /** @test */
    public function users_can_fully_confirm_their_email_address()
    {
        Mail::fake();
        
        $this->post(route('register'), [
            'name' => 'Gary',
            'email' => 'gary@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::whereName('Gary')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
            ->assertRedirect(route('thread.index'));

        $this->assertTrue($user->fresh()->confirmed);
        $this->assertNull($user->fresh()->confirmation_token);
    }
}
