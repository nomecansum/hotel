<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class RedirectIfAuthenticated
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
        
        if (Auth::guard($guard)->check()) {
            
            if($request->session()->has('redirectTo')){
                return redirect($request->session()->get('redirectTo'));
            }
            return redirect(RouteServiceProvider::HOME);
        }
        return $next($request);
    }
}
