<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Storage;

class HomeController extends Controller {
	public function index(Request $request){
    $user = Auth::user();
    $siteconfig = $this->siteconfig;
		return view('welcome', compact('user','siteconfig'));
	}
  public function refresh( Request $request){
     session()->regenerate();
     return response()->json([
        "token"=>csrf_token()],
      200);
  }
}