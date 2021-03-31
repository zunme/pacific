<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('refresh-token', 'App\Http\Controllers\Front\HomeController@refresh')->name('refresh-token');

Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
Route::get('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::get('recommender', 'App\Http\Controllers\Front\Recommendation@getData');

Route::prefix('/join')->name('join.')->group(function () {
    Route::get('/', 'App\Http\Controllers\Front\UserController@showRegisterForm')->name('index');
    Route::post('/', 'App\Http\Controllers\Front\UserController@register');
});

Route::middleware(['auth:web','penalty'])
	 ->group(function () {
     
      Route::get('/', 'App\Http\Controllers\Front\HomeController@index')->name('home');
     
      /* 상품 예약 */
      Route::get('/product', 'App\Http\Controllers\Front\ProductController@productlist')->name('product');
      Route::get('/product/{product_id}', 'App\Http\Controllers\Front\ProductController@productItem');
     
      Route::post('/frontapi/reservation', 'App\Http\Controllers\Front\ProductController@reservation');
     //구매상품
     Route::get('/my/buylist', 'App\Http\Controllers\Front\ProductController@buylist')->name('buylist');
     Route::get('/my/buylist/history', 'App\Http\Controllers\Front\ProductController@buyHistory')->name('buylisthistory');
     Route::get('/my/buylist/detail/{trading_code}', 'App\Http\Controllers\Front\ProductController@buyDetail')->name('buydetail');
     
     Route::post('/my/buylist/depoist', 'App\Http\Controllers\Front\ProductController@depoist');
     //판매상품
     Route::get('/my/salelist', 'App\Http\Controllers\Front\ProductController@salelist')->name('salelist');
     Route::get('/my/salelist/history', 'App\Http\Controllers\Front\ProductController@saleHistory')->name('salelisthistory');
     Route::get('/my/salelist/detail/{trading_code}', 'App\Http\Controllers\Front\ProductController@saleDetail')->name('saledetail');
     
     Route::post('/my/trade/cmpt', 'App\Http\Controllers\Front\ProductController@trade');
     
  }
);




Route::prefix('/adm')->name('admin.')->group(function () {
  Route::get('/', function() {
    return view ( 'layouts.admin');
  });
  //설정
  Route::get('/config', 'App\Http\Controllers\Admin\AdminController@configPage')->name('config');
  Route::post('/api/config/save', 'App\Http\Controllers\Admin\AdminController@configsave');
  //상품설정
  Route::get('/product/config', 'App\Http\Controllers\Admin\AdminController@productConfigPage')->name('product.config');
  Route::get('/product/list', 'App\Http\Controllers\Admin\AdminController@productList')->name('product.list');
  Route::post('/api/product/save', 'App\Http\Controllers\Admin\AdminController@productSave');
  //회원
  Route::get('/member', 'App\Http\Controllers\Admin\AdminController@member')->name('member');
  Route::get('/member/list', 'App\Http\Controllers\Admin\AdminController@memberList')->name('member.list');
  Route::post('/api/member/save', 'App\Http\Controllers\Admin\AdminController@memberSave')->name('member.save');
  Route::post('/api/member/pwd', 'App\Http\Controllers\Admin\AdminController@memberPasswordChange')->name('member.pwd');
  //패널티
  Route::post('/api/penalty/{penaltytype}', 'App\Http\Controllers\Admin\AdminController@addPenalty')->where('penaltytype', 'reset|sale|purchase');
  //포인트
  Route::post('/api/point/{pointprc}', 'App\Http\Controllers\Admin\AdminController@pointprc')->where('pointprc', 'plus|minus');
  
  //예약리스트
  Route::get('/reservation', 'App\Http\Controllers\Admin\ProductController@reservation')->name('reservation');
  Route::get('/reservation/list', 'App\Http\Controllers\Admin\ProductController@reservationList')->name('reservation.list');
  
  Route::post('/api/matching/adm', 'App\Http\Controllers\Admin\ProductController@matchingAdmin')->name('matching.adm');//관리자매칭
  Route::post('/api/matching/cancel_reservation', 'App\Http\Controllers\Admin\ProductController@cancelReservation')->name('matching.cancel.reservation');//구매예약삭제,취소
  
  
});