<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyAndSetCompany
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
        $user = Auth()->user();
        $companyID = $request->route('company_id');

        if($user->user_type != 'admin' && $user->company_id != $companyID) {
            abort(404, 'Page not found');
        }

        $company = Company::findOrFail($companyID);
        view()->share('companyID', $companyID);
        view()->share('companyLogo', $company->logo_path);
        return $next($request);
    }
}
