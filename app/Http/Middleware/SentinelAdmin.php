<?php namespace App\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;

class SentinelAdmin
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // First make sure there is an active session
        if ( ! Sentinel::check()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        }

        // Now check to see if the current user has the 'admin' permission
        if ( ! Sentinel::getUser()->hasAccess('admin')) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
