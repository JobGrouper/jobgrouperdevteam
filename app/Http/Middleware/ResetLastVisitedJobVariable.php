<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;


class ResetLastVisitedJobVariable
{

    private $exceptRoutes = [
        'login',
        'auth/login',
        'social_login/{provider}',
        'social_login/callback/{provider}',
        'register',
        'custom_register',
        'job/{id}',
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currentPath = Route::getFacadeRoot()->current()->uri();
        if(!in_array($currentPath, $this->exceptRoutes)){
            Session::forget('last_visited_job');
        }

        return $next($request);
    }
}
