<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_reply_that_is_not_by_the_current_user()
    {
        $this->signIn();
        $thread = create('App\Thread')->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some reply body.'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some reply body.'
        ]);
        
        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_their_unread_notifications()
    {
        $this->signIn();
        $notification = create(DatabaseNotification::class);

        $response = $this->getJson(route('notification.index', auth()->user()))->json();

        $this->assertCount(1, $response);
    }

    /** @test */
    public function an_authorized_user_can_mark_a_notification_as_read()
    {
        $this->signIn();
        $notification = create(DatabaseNotification::class);

        $this->assertCount(1, auth()->user()->unreadNotifications);

        $this->delete(route('notification.destroy', [auth()->user(), $notification]));

        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }

    /** @test */
    public function an_unauthorized_user_cannot_mark_a_notification_as_read()
    {
        $this->signIn();
        $subscribedUser = create('App\User');
        $notification = create(DatabaseNotification::class, ['notifiable_id' => $subscribedUser->id]);

        $this->assertCount(1, $subscribedUser->unreadNotifications);

        $this->withExceptionHandling()
            ->delete(route('notification.destroy', [$subscribedUser, $notification]))
            ->assertStatus(403);

        $this->assertCount(1, $subscribedUser->fresh()->unreadNotifications);
    }
}
