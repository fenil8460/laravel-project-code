<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Services\AdminService;
use App\Traits\ResponseAPI;

class CheckAdmin
{
    use ResponseAPI;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(isset(Auth::guard('admin')->user()->id)){
            return $next($request);
        }
        else{
            return $this->error([
                'auth' => 'Unauthorized Admin',
            ],'404');

        }
    }
}
