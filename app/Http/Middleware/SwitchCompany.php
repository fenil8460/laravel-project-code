<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Services\CompanyService;
use App\Traits\ResponseAPI;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class SwitchCompany
{
    use ResponseAPI;

    public function __construct()
    {
        $this->company_service = new CompanyService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        $company = Company::where('uu_id',$request->companyId)->where('user_id',Auth::user()->id)->first();
        if(isset($company->id)){
            return $next($request);
        }else{
            return $this->error("You haven't swiched to a company",'404');
        }
    }
}
