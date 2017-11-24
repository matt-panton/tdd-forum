<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserAvatarRequest;

class UserAvatarController extends Controller
{
    /**
     * Create a new UserAvatarController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Upload and store avatar image for given user.
     *
     * @param App\User 
     * @return Illuminate\Http\Response
     */
    public function store(UserAvatarRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->removeExistingAvatar()
            ->update([
                'avatar_path' => $request->file('avatar')->store('avatars', 'public')
            ]);

        return response()->json(null, 200);
    }
}
