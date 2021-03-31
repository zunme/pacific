<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\SiteConfig;

class Penalty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
      
        $user = Auth::user();
        $siteconfig = SiteConfig::first();

       if( $user->lslock =='Y' || $user->login_available =='N' 
           ||  $user->penalty_sale >= $siteconfig->penalty_sale  || $user->penalty_purchase >= $siteconfig->penalty_purchase ){
          Auth::logout();
         if ($request->ajax()) {
            return response()->json(['status'=>'Error','msg'=>'현재 회원님은 로그인이 불가능한 상태입니다.','data'=>['penalty'=>true] ], 401);
          }
          return response( view( 'penalty', compact(['user','siteconfig']) ) );
        }
        return $next($request);
    }
}
