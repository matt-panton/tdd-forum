<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;

class NotifySubscribers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        $reply = $event->reply;

        $reply->thread->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each
            ->notify($reply);
    }
}
