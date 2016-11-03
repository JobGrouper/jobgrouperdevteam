<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SetLocalization
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
        if (false/*::has('applocale') AND array_key_exists(Session::get('applocale'), Config::get('languages'))*/) {
            App::setLocale(Session::get('applocale'));
        }
        else {
            $ipInfo = geoip()->getLocation($request->ip());
            if($ipInfo->country == 'China'){
                App::setLocale('cn');
            }
            else{
                App::setLocale('en');
            }
        }

        return $next($request);
    }
}
