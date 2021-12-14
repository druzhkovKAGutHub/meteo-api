<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Response;

class Cors
{
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('OPTIONS')){
            $response = Response::make();
        } else {
            $response = $next($request);
        }
        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', '*');

        /*return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', '*');*/
    }
}
//X-Requested-With, Content-Type, X-Token-Auth, Authorization, Content-Length, Connection, Content-Disposition, Origin
