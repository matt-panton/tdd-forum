<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class ThreadSearchController extends Controller
{
    public function index()
    {
        return request()->wantsJson()
            ? Thread::search(request('q'))->paginate(25)
            : view('thread.search');
    }
}
