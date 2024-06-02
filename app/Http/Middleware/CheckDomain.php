<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDomain
{
    
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $fullDomain = request()->getHttpHost();
        $adminAppDomain = explode(',', env('ADMIN_APP_DOMAIN'));
        $adminPublisherDomain = explode(',', env('PUBLISHER_DOMAIN'));

        $user = Auth::guard('web')->user();
        if(empty($user)){
             return $next($request);
        }
        if($user->user_type == 'admin' && in_array($fullDomain, $adminAppDomain)){
            return $next($request);
        }else if($user->user_type == 'publisher' && in_array($fullDomain, $adminPublisherDomain)){
            return $next($request);
        }
        Auth::logout();
        return redirect('/login')->with('status', 'Credential Unauthorized')->with('level', 'danger');
    }
}
