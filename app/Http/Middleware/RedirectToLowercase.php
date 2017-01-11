<?php

namespace App\Http\Middleware;

use Closure;

class RedirectToLowercase
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
        $path = $request->path();
        $pathLowercase = strtolower($path); // convert to lowercase

        if ($path !== $pathLowercase) {
            // redirect if lowercased path differs from original path
            return redirect($pathLowercase);
        }
        return $next($request);
    }
}
