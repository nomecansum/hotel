<?php

namespace App\Http\Middleware;

use Closure;

class Permissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $secciones=NULL, $permisos=NULL)
    {
        if($request->wantsJson()){  //API
            if (checkPermissionsAPI(json_decode($secciones),json_decode($permisos))) {
                return $next($request);
            }
        } else {  //WEB
            if (checkPermissions(json_decode($secciones),json_decode($permisos))) {
                return $next($request);
            }
        }
        return back();
    }
}
