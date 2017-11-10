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
}
