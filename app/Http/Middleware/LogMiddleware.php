<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Log;

use Closure;
use Illuminate\Support\Facades\Auth;

class LogMiddleware
{
    public function handle($request, Closure $next)
    {
       /* $user =  User::first();//find($user_id);
        Auth::setUser($user);
*/
        Log::info([
            'user' => $request->user(),
        ]);
        Log::info([
            'request' => $request,
        ]);

        return $next($request);
    }
}

