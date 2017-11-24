<?php

namespace App\Policies;

use App\User;
use App\Reply;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, Reply $reply)
    {
        return $user->owns($reply);
    }

    public function update(User $user, Reply $reply)
    {
        return $user->owns($reply);
    }

    public function store(User $user)
    {
        $lastReply = $user->fresh()->lastReply;

        if (is_null($lastReply)) {
            return true;
        }

        return !$lastReply->wasJustPublished();
    }
}
