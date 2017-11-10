<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth');
    }

    public function store(Reply $reply)
    {
        $reply->favourite();

        return redirect()->back();
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavourite();

        if (request()->wantsJson()) {
            return response()->json(null, 200);
        }
        
        return redirect()->back();
    }
}
