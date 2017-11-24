<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth');
    }

    public function store(Channel $channel, Thread $thread)
    {
        $thread->subscribe(request()->user());

        if (request()->wantsJson()) {
            return response()->json(null, 200);
        }

        return redirect()->back();
    }

    public function destroy(Channel $channel, Thread $thread)
    {
        $thread->unsubscribe(request()->user());

        if (request()->wantsJson()) {
            return response()->json(null, 200);
        }

        return redirect()->back();
    }
}
