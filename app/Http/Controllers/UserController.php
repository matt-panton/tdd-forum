<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user)
    {
        $user->setRelation('activity', Activity::feed($user));

        return view('user.show', compact('user'));
    }
}
