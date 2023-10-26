<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $fullDomain = $request->getHost();
        $user = Auth::guard('web')->user();
        if(empty($user)){
             return $next($request);
        }
        if($user->user_type == 'admin' && $fullDomain == env('FULL_DOMAIN')){
            return $next($request);
        }else if($user->user_type == 'publisher' && $fullDomain == env('SUB_DOMAIN')){
            return $next($request);
        }
        
        return response()->json([
                'message' => 'Unauthorized.',
            ], 401);;
    }
}
