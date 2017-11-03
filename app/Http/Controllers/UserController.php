<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user)
    {
        $user->setRelation('threads', $user->threads()->paginate(15));

        return view('user.show', compact('user'));
    }
}
