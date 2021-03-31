<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;
use Validator;

use Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Product;
use App\Models\ProductItem;
use App\Models\BuyReservation;
use App\Models\Trading;
use App\Modules\salelistModule;

class ProductController extends Controller
{
  use ApiResponser;
  function reservation (Request $request){
    $siteconfig = $this->siteconfig;
    $products = Product::where('is_use','Y')->get();
    return view('admin.reservation', compact(['siteconfig','products']));
  }
  
  function reservationList (Request $request){
    $data = BuyReservation::
            select('buy_reservations.id', 'buy_reservations.user_id','buy_reservations.amount','buy_reservations.reservation_status','buy_reservations.created_at',
                   'products.product_name',
                   'users.phone', 'users.point'
                  )
            ->join ('products', 'buy_reservations.product_id','=','products.id')
            ->join ('users', 'buy_reservations.user_id','=','users.id')
            ->where('reservation_status','R')
            ;
    if( $request->search_keyword){
      $data->where('users.phone','like', '%'.$request->search_keyword.'%');
    }
    if( $request->product_id ){
      $data->where('buy_reservations.product_id',$request->product_id);
    }
    return Datatables::of($data)
                ->addIndexColumn()
                //->with(['today'=>Carbon::now()->format('Y-m-d')])
                ->rawColumns(['action'])
                ->make(true);    
  }
  function salelist(Request $request){
    $salelistModule =new salelistModule();
    $data =  $salelistModule->salelist() ;
    
   return Datatables::of($data)
            ->make(true); 
  }
  //구매예약취소
  function cancelReservation(Request $request){
    $reserv = BuyReservation::findOrFail( $request->reserve_id );
    $check = Trading::where('reservation_id', $reserv->id )->count();
    if( $check ){
      return $this->error('이미 매칭된 기록이 있습니다. F5를 눌러서 재로딩 해주세요',422);
    }
    try{
      $reserv->reservation_status = 'N';
      $reserv->save();
      return $this->success();
    } catch (\Exception $e) {
      return $this->error();
    }
  }
  //관리자매칭
  function matchingAdmin( Request $request){
    $reserv = BuyReservation::findOrFail( $request->reserve_id );
    $products = Product::findOrFail( $reserv->product_id );
    
    $checkTrdSum =Trading::where('reservation_id', $reserv->id)->where('trading_status','!=','N')->sum('amount');
    $amount = $reserv->amount - $checkTrdSum;
    if( $amount < 1){
      return $this->error('모두 매칭되었습니다.',422);
    }
    $price = $products->price;
    $totalprice = $price * $amount;

    $tradingCode = str_replace('.','_',uniqid('T'.$reserv->id, true)) ;
    \DB::beginTransaction();
    try{
    
        $trading = Trading::create([
          'trading_code'=>$tradingCode, 'reservation_id'=>$reserv->id,
          'buy_user_id'=>$reserv->user_id,'seller_user_id'=>1, 'product_id'=>$reserv->product_id,
          'amount'=>$amount, 'price'=>$totalprice, 'trading_status'=>'MATCHED'
        ]);
        $item = [
          'product_id'=>$reserv->product_id,
          'user_id'=>1, 'buy_price'=>0, 'sell_price'=>$price,
          'buy_date'=>Carbon::today()->format('Y-m-d'),
          'trading_id'=> 0, 'trading_reserved_id'=>$trading->id
        ];
        for($i=0; $i < $amount; $i++){
          ProductItem::create( $item);
        }
        $reserv->update(['reservation_status'=>'MATCHED']);
        \DB::commit();
      return $this->success();
    } catch (\Exception $e) {
		  \DB::rollback();
      dd( $e);
      return $this->error();
		}
  }
}