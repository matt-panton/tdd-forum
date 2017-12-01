<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use App\Channel;
use App\Http\Requests\ReplyRequest;

class ReplyController extends Controller
{
    /**
     * Create a new ReplyController instance.
     */
    public function __construct ()
    {
        $this->middleware('auth')->except('index');
    }

    /**
     * Get a list of replies for given thread.
     * 
     * @param  App\Channel  $channel
     * @param  App\Thread   $thread
     * @return Illuminate\Http\Response
     */
    public function index(Channel $channel, Thread $thread)
    {
        $replies = $thread->replies()->paginate(20);

        $replies->each(function ($reply) use ($thread) {
            $reply->setRelation('thread', $thread);
        });

        return $replies;
    }

    /**
     * Store a new reply in the database.
     * 
     * @param  App\Channel  $channel
     * @param  App\Thread   $thread 
     * @param  App\Http\Requests\ReplyRequest  $request
     * @return App\Reply
     */
    public function store(Channel $channel, Thread $thread, ReplyRequest $request)
    {
        if ($thread->locked) {
            return response('Thread is locked.', 422);
        }

        return $thread->addReply([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ])->load('user');
    }

    /**
     * Persis updates to a reply.
     * 
     * @param  App\Reply  $reply
     * @param  App\Http\Requests\ReplyRequest  $request
     * @return Illuminate\Http\Response
     */
    public function update(Reply $reply, ReplyRequest $request)
    {
        $this->authorize('update', $reply);
        
        $reply->update($request->only('body'));

        return response()->json($reply->fresh(), 200);
    }

    /**
     * Delete a reply.
     * 
     * @param  App\Reply  $reply
     * @return Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('destroy', $reply);

        $reply->delete();

        return response()->json(null, 200);
    }
}
