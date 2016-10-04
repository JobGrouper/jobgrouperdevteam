<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class BlockedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = Auth::user();
        if ($user and !$user->active) {
            Auth::logout();
            return redirect('/login')->with('message','Your account has blocked! Contact the support!');
        }

        return $next($request);
    }
}
