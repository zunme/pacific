<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

use Carbon\Carbon;
use Validator;

use Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


use App\Models\User;
use App\Models\Product;
use App\Models\BuyReservation;
use App\Models\Trading;

use App\Modules\traderModule;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

use App\Modules\salelistModule;
class ProductController extends Controller
{
  use ApiResponser;
  
  function productlist( Request $request){
    $siteconfig = $this->getSiteConfig();
    $products = Product::where('is_use','!=','N')->get();
    $user = Auth::user();
    return view('product', compact(['user','siteconfig','products']) );
  }
  function productItem( Request $request, $product_id){
    
    $siteconfig = $this->getSiteConfig();
    $user = Auth::user();
    
    $product = Product::where('id', $product_id)->first();
    if(!$product) {
      return abort(404);
    }
    return view('productbuy', compact(['user','siteconfig','product']) );
  }
  /* 구매예약 */
  function reservation(Request $request){
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    
    if( !$this->checkAvailTime() ) return $this->error('거래가능 시간이 아닙니다.',422);
    
    if( (int)$request->amount < 1) {
      return $this->error('구매 수량을 확인해주세요');
    }
    $prd_id = (int)$request->product_id;
    
    $product = Product::findOrFail( $prd_id );
    
    if( $siteconfig->trading_limit > 0 ) {
      $cnt = BuyReservation::where('user_id',$user->id )
        ->whereDate('created_at', Carbon::today())->count();
      if( $cnt >= $siteconfig->trading_limit ){
        return $this->error('이미 총 '.$cnt.'건의 구매예약이 있습니다.');
      }
    }
     
    $data = array();
    $data['user_id'] = $user->id;
    $data['reservation_code'] =  str_replace('.','_',uniqid($user->id, true)) ;
    $data['product_id'] = $prd_id;
    $data['amount'] = (int)$request->amount;
    
    try{
      BuyReservation::create($data);
      return $this->success(); 
    } catch (\Exception $e) {
      dd($e);
      return $this->error(); 
    }
  }
  
  function buylist (Request $request){
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    
    $products = \DB::select("
        SELECT 
          products.*,
          IFNULL( tmp.reserv,0) reserv,
          IFNULL( tmp2.matched,0) matched,
          IFNULL( tmp2.waiting,0) waiting,
          IFNULL( tmp2.completed,0) completed
        FROM products
        LEFT JOIN (
          SELECT 
            product_id,
            count(1) reserv
          FROM buy_reservations a
          WHERE user_id = ".$user->id." 
            AND reservation_status = 'R'
          GROUP BY product_id
        ) tmp ON products.id = tmp.product_id
        LEFT JOIN (
          SELECT 
            product_id,
            SUM( if(  trading_status = 'MATCHED', 1,0) ) AS matched,
            SUM( if(  trading_status = 'AWAITING', 1,0) ) AS waiting,
            SUM( if(  trading_status = 'CMPT', 1,0) ) AS completed
          FROM tradings b
          WHERE buy_user_id = ".$user->id."
          AND trading_status !='N'
          group by product_id
        ) tmp2 ON products.id = tmp2.product_id
        WHERE is_use ='Y'  
    ");
    /*
    $types=['R'=>'구매신청','MATCHED'=>'입금대기','AWAITING'=>'승인대기','CMPT'=>'구매완료'];
    $data = BuyReservation::
        select( 'buy_reservations.*', 'products.product_name')
        ->join('products', 'buy_reservations.product_id','=','products.id')
        ->where('user_id', $user->id)->where('reservation_status','!=','N')
        ->latest()->paginate(1);
        */
    return view('buylist', compact(['user','products','siteconfig']));
  }

  function buyHistory (Request $request){
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    
    $types= config('sitedata.tadting_types');
    if( $request->page < 2){
      $reservedata = BuyReservation::
          select( 'buy_reservations.*', 'products.product_name')
          ->join('products', 'buy_reservations.product_id','=','products.id')
          ->where('user_id', $user->id)->where('reservation_status','R')
         // ->where(\DB::raw( "DATE(buy_reservations.created_at) = CURDATE()  "))
          ->latest()->get();//->paginate(1);
    } else $reservedata = [];
    $data = Trading::        select( 'tradings.*', 'products.product_name')
        ->join('products', 'tradings.product_id','=','products.id')
        ->where('buy_user_id', $user->id)->where('trading_status','!=','N')
        ->latest()->paginate(2);
    return view('buyhistory', compact(['data','types','reservedata','siteconfig']));
    //return $this->success( $data );
  }
  
  function buyDetail (Request $request, $trading_code){
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    
    $types= config('sitedata.tadting_types');
    $data = Trading::select( 'tradings.*', 'products.product_name', 'image_url', 'deposit_file', \DB::raw('seller.phone as sellerPhone') )
      ->join('products', 'tradings.product_id','=','products.id')
      ->join('users as seller', 'tradings.seller_user_id','=','seller.id')
      
      ->where('trading_code',$trading_code)
      ->where('buy_user_id', $user->id)->first();
    if( !$data){
      return abort(404);
    }
    return view('buydetail', compact(['user','data','types','siteconfig']));
  }
  //입금증업로드
  function depoist(Request $request) {
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    
    $messages = [      
      'select_img.required'=>"입금증 파일을 올려주세요",
      'select_img.*'=>"올바른 입금증 파일을 올려주세요",
    ];    
    $validation = $request->validate([
      'select_img' => 'bail|required|image|mimes:jpeg,jpg,gif,png|max:5000',
    ],$messages);   
    
    $data = Trading::where('id', $request->trading_id )->where('buy_user_id', $user->id)->first();

    if( !$data) return $this->error('거래 내역을 찾을 수 없습니다.', 422);
    if( $data->trading_status == 'R' ) return $this->error('매칭 대기중입니다.', 422);
    if( $data->trading_status == 'CMPT' ) return $this->error('이미 구매완료 되었습니다..', 422);
    if( $data->trading_status != 'MATCHED' ) return $this->error('이미 입금증을 올리셨습니다.', 422);
    
    try{
      $file = $request->file('select_img');
      $image = Image::make($file);
      $image_name = Carbon::now()->timestamp.'_'. $data->trading_code.'.'.$file->getClientOriginalExtension();
      $path = 'deposit/'. $image_name;
      Storage::disk('public')->put($path, $image->stream()->__toString());

      $data->update([
        'deposit_file' => $path,
        'trading_status'=>'AWAITING',
        'deposit_at'=>Carbon::now()      
      ]);
      return $this->success();
    } catch (\Exception $e){
      dd( $e);
      return $this->error();
    }
  }
  
  
  //판매
  function salelist(Request $request){
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    $salelistModule =new salelistModule();
    $needPoint = $salelistModule->needPoint($user->id);
    if( !$needPoint ){
      $reserv = 'NONE';
    }else if ( $needPoint['need_point'] > $user->point ){
      $reserv = 'POINT_MORE';
    } else {
      $reserv = 'DATA';
    }
    
    $saleNeedPoint =  $salelistModule->needPoint($user->id);
    $saleList = $salelistModule->userSaleAvaiInfo($user->id) ;
    dd( $saleList);
    $products = \DB::select("
        SELECT 
          products.*,
          0 reserv,
          IFNULL( tmp2.matched,0) matched,
          IFNULL( tmp2.waiting,0) waiting,
          IFNULL( tmp2.completed,0) completed
        FROM products
        LEFT JOIN (
          SELECT 
            product_id,
            SUM( if(  trading_status = 'MATCHED', 1,0) ) AS matched,
            SUM( if(  trading_status = 'AWAITING', 1,0) ) AS waiting,
            SUM( if(  trading_status = 'CMPT', 1,0) ) AS completed
          FROM tradings b
          WHERE seller_user_id = ".$user->id."
          AND trading_status !='N'
          group by product_id
        ) tmp2 ON products.id = tmp2.product_id
        WHERE is_use ='Y'  
    "); 
    return view('salelist', compact(['user','products','siteconfig']));
  }
  function saleHistory (Request $request){
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    
    $types= config('sitedata.tadting_types');
    if( $request->page < 2){
      $reservedata = [];
    } else $reservedata = [];
    $data = Trading:: select( 'tradings.*', 'products.product_name')
        ->join('products', 'tradings.product_id','=','products.id')
        ->where('seller_user_id', $user->id)->where('trading_status','!=','N')
        ->latest()->paginate(2);
    return view('salehistory', compact(['data','types','reservedata','siteconfig']));
    //return $this->success( $data );
  }
  function saleDetail (Request $request, $trading_code){
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    
    $types= config('sitedata.tadting_types');
    $data = Trading::select( 'tradings.*', 'products.product_name', 'image_url','deposit_file', \DB::raw('buyer.phone as buyerPhone') )
      ->join('products', 'tradings.product_id','=','products.id')
      ->join('users as buyer', 'tradings.buy_user_id','=','buyer.id')
      
      ->where('trading_code',$trading_code)
      ->where('seller_user_id', $user->id)->first();
    if( !$data){
      return abort(404);
    }
    $secret = Crypt::encryptString($data->id);
    return view('saledetail', compact(['user','data','types','siteconfig', 'secret']));
  }  
  
  //거래완료
  function trade(Request $request ) {
    $user = Auth::user();
    $siteconfig = $this->getSiteConfig();
    
    try {
        $trade_id = Crypt::decryptString($request->code);
    } catch (DecryptException $e) {
        return $this->error('거래내역을 찾을 수 없습니다.', 422);
    }
      $traderModule = new traderModule($trade_id , $user->id );
      $res = $traderModule->trade();
      if( $res['code'] == 'Success') return $this->success();
      else return $this->error($res['msg'], 422);
  }
}