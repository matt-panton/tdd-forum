<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfEmailNotConfirmed
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
        if (is_null($request->user()) || !$request->user()->confirmed) {
            return redirect()->route('thread.index')->with('flash', 'You must first confirm your email address.');
        }

        return $next($request);
    }
}