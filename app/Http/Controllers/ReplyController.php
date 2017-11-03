<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use App\Channel;
use App\Http\Requests\ReplyRequest;

class ReplyController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth');
    }

    public function store(Channel $channel, Thread $thread, ReplyRequest $request)
    {
        $thread->addReply([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back();
    }
}
