<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Filters\ThreadFilters;
use App\Repositories\Trending;
use App\Http\Requests\ThreadRequest;

class ThreadController extends Controller
{
    /**
     * Create a new ThreadController instance.
     */
    public function __construct ()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('must-be-confirmed')->only('store');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Channel               $channel
     * @param \App\Filters\ThreadFilter  $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters);

        return request()->wantsJson()
            ? $threads
            : view('thread.index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('thread.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ThreadRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ThreadRequest $request)
    {
        $thread = Thread::create([
            'user_id' => $request->user()->id,
            'channel_id' => $request->channel_id,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        if ($request->wantsJson()) {
            return response()->json($thread, 201);
        }

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Channel  $channel
     * @param  \App\Thread   $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Channel $channel, Thread $thread, Trending $trending)
    {
        $thread->append('is_subscribed_to');

        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->increment($thread);
        $thread->increment('visits');

        return view('thread.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Channel $channel, Thread $thread, ThreadRequest $request)
    {
        $this->authorize('update', $thread);

        $thread->update([
            'channel_id' => $request->get('channel_id', $thread->channel_id),
            'title' => $request->get('title', $thread->title),
            'body' => $request->get('body', $thread->body),
        ]);

        if ($request->wantsJson()) {
            return response()->json($thread, 201);
        }

        return redirect($thread->fresh()->path())
            ->with('flash', 'Your thread has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Channel  $channel
     * @param  \App\Thread   $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel, Thread $thread)
    {
        $this->authorize('destroy', $thread);

        $thread->delete();

        return request()->wantsJson()
            ? response()->json(null, 200)
            : redirect()->route('user.show', auth()->user());
    }

    /**
     * Get collection of filtered threads.
     * 
     * @param  App\Channel                $channel
     * @param  App\Filters\ThreadFilters  $filters
     * @return Illuminate\Database\Eloquent\Collection
     */
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::latest();

        if ($channel->exists) {
            $threads = $threads->where('channel_id', $channel->id);
        }

        return $threads->filter($filters)->paginate(5);
    }
}
