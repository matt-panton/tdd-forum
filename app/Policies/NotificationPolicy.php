<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    use HandlesAuthorization;

    public function destory(User $user, DatabaseNotification $notification)
    {
        return $notification->notifiable_type == 'App\User' && (int)$notification->notifiable_id === $user->id;
    }
}
