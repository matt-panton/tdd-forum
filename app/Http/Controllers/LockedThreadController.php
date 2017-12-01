<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use Illuminate\Http\Request;

class LockedThreadController extends Controller
{
    public function __construct ()
    {
        $this->middleware('admin');
    }

    public function store(Thread $thread)
    {
        $thread->update(['locked' => true]);

        return request()->wantsJson()
            ? response()->json(null, 200)
            : redirect()->back();
    }

    public function destroy(Thread $thread)
    {
        $thread->update(['locked' => false]);

        return request()->wantsJson()
            ? response()->json(null, 200)
            : redirect()->back();
    }
}
