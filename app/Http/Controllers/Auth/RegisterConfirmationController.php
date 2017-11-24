<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationConfirmationRequest;

class RegisterConfirmationController extends Controller
{
    public function store(RegistrationConfirmationRequest $request)
    {
        $user = User::where('confirmation_token', $request->token)->first()->confirm();

        return redirect()->route('thread.index')
            ->with('flash', 'Your account is now confirmed! You may post to the forum.');
    }
}
