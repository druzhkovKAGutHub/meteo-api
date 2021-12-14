<?php
namespace App\Http\Middleware;
use Closure;
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!$request->user()->hasPermission($permission)) {
            return response()->json([
                'error' => 'Не достаточно прав'
            ], 403);
        }
        return $next($request);
    }
}