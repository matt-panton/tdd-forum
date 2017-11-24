<?php

namespace App\Listener;

use App\Mail\ConfirmYourEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class SendEmailConfirmationRequest
{
    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        Mail::to($event->user)
            ->send(new ConfirmYourEmail($event->user));
    }
}
