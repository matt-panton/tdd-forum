<?php

namespace App\Http\Middleware;

use Closure;

class Administrator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_null($request->user()) || !$request->user()->isAdmin()) {
            return $request->wantsJson()
                ? response()->json('You must be an administrator to perform this action.', 403)
                : redirect()->route('thread.index');
        }

        return $next($request);
    }
}
