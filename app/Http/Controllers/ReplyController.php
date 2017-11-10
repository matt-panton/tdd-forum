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
        $this->middleware('auth')->except('index');
    }

    public function index($channel, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    public function store($channel, Thread $thread, ReplyRequest $request)
    {
        $reply = $thread->addReply([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        if ($request->wantsJson()) {
            return $reply->load('user');
        }

        return redirect()->back()
            ->with('flash', 'Your reply has been left.');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        
        $reply->update(request()->only('body'));

        return response()->json(null, 200);
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('destroy', $reply);

        $reply->delete();

        if (request()->wantsJson()) {
            return response()->json(null, 200);
        }

        return redirect()->back();
    }
}
