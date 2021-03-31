<?php

namespace App\Modules;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Trading;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\ProductItemHistory;
use App\Models\PointHistory;
use App\Models\Recommender;

use App\Models\SiteConfig;

class traderModule {
  protected $trade;
  protected $items,$product, $calc_fee, $calc_profit ;
  
  public function __construct( $trade_id, $seller_id) {
    $trade = Trading::where( 'id', $trade_id)->where('seller_user_id', $seller_id )->where('trading_status', 'AWAITING')->first();
    $items = ProductItem::where( 'user_id', $trade->seller_user_id)->where('trading_reserved_id', $trade->id )->where('is_use', 'Y');
    $this->product = Product::where('id', $trade->product_id )->first();
    
    $this->trade = $trade;
    $this->items = $items;
    $this->calc_profit = $this->calcProfit(config("sitedata.calc_profit"));
    $this->calc_fee = $this->calcProfit(config("sitedata.calc_fee"));
  }
  
  public function trade() {   
    if( !$this->trade ) return ['code'=>'Error', 'msg'=>'거래내역을 찾을 수 없습니다.'];
    if( $this->items->count() < 1) return ['code'=>'Error', 'msg'=>'거래가능한 상품을 찾을 수 없습니다.'];
    
    $now = Carbon::now()->format('Y-m-d H:i:s');
    
    $seller = User::find( $this->trade->seller_user_id);
    $fee = $this->calcFee( );

    $pointHistory = ['user_id'=>$seller->id , 'point_type'=> 'U',
                 'amount'=>$fee['fee']*-1 ,'before_amount'=> $seller->point, 'after_amount'=>$seller->point - $fee['fee'],
                 'trade_id'=>$this->trade->id ];

    \DB::beginTransaction();
    try {
      $histories = $this->items;
      $history = $histories
        ->select('id', 'product_id', 'parent_item_id', 'user_id', 'buy_price', 'sell_price','buy_date','holding_period',
                 DB::raw('trading_reserved_id as trading_id'), 'is_use' , 
                 DB::raw('"'.$now.'" as traded_at')
                );
      $bindings = $history->getBindings();
      $insertQuery = 'INSERT into product_items_histories (item_id, product_id,parent_item_id,user_id,buy_price,sell_price, buy_date, holding_period , trading_id, is_use ,traded_at ) '
                    . $history->toSql();
      DB::insert($insertQuery, $bindings);
      $items =  $this->items;
      $item = $items->update([
        'user_id'=> $this->trade->buy_user_id , 'buy_date'=> Carbon::now()->format('Y-m-d') , 
        'holding_period'=> 0, 'trading_id'=>$this->trade->id, 'trading_reserved_id'=>0
        , 'traded_at'=>$now , 'trading_id'=>DB::raw('trading_reserved_id') , 'trading_reserved_id'=> 0,
        'buy_price'=> DB::raw('sell_price') ,
        'sell_price'=> DB::raw( "sell_price + ".$this->calc_profit."(sell_price * ".$this->product->profit_rate." /100) ") ,
        ]);
      $this->trade->update(['trading_status'=>'CMPT', 'completed_at'=>$now]);
        
      $seller->point = $seller->point - $fee['fee'];
      $seller->save();
      
      PointHistory::create( $pointHistory );
      $addrcmd = $this->addRcmdPoint( $seller , $fee, $this->trade->id );
      \DB::commit();
      return ['code'=>'Success', 'msg'=>'거래가 완료되었습니다.']; 
    } catch ( \Exception $e ){
      \DB::rollback();
      return ['code'=>'Error', 'msg'=>'잠시후에 이용해주세요.'];
    }
  }
  
  function calcProfit( $calc ){
    if($calc=="올림") return 'ceil' ; //올림
    else if($calc=="버림") return 'floor'; //버림
    else return 'round'; //반올림
  }
  function calcFee(){
    $ret = ['fee'=>0, 'rcmd_1'=>0, 'rcmd_2'=>0];
    $siteconfig = SiteConfig::first();
    $fn = $this->calc_fee;
    //소수점 자리수
    if( config("sitedata.decimalPlace") == 0 ) {
      $firstNum = 100; $secNum = 1;
    } else if( config("sitedata.decimalPlace") == 1 ) {
      $firstNum = 10; $secNum = 10;
    }else {$firstNum = 1; $secNum = 100;}
    $fee = (int)$fn($this->trade->price * $this->product->fee  / $siteconfig->price_per_point / $firstNum);
    $ret['fee'] = $fee/$secNum;
    $ret['rcmd_1'] = floor($ret['fee'] * config("sitedata.rcmd_depth1")/ $firstNum) / $secNum;
    $ret['rcmd_2'] = floor($ret['fee'] * config("sitedata.rcmd_depth2")/ $firstNum) / $secNum;
    return $ret;
  }
  function addRcmdPoint( $seller , $fee , $trade_id ){
    $rcmd = Recommender::where('user_id' , $seller->id)->first();
    if ( $rcmd && $rcmd->parent_step1_id > 0 ) $this->addRcmdPointPrc( $rcmd->parent_step1_id, $fee['rcmd_1'] ,'R1', $trade_id);
    if ( $rcmd && $rcmd->parent_step2_id > 0 ) $this->addRcmdPointPrc( $rcmd->parent_step2_id, $fee['rcmd_2'] ,'R2', $trade_id);
  }
  function addRcmdPointPrc( $user_id, $point, $type, $trade_id ){
    $user = User::where('id', $user_id)->first();
    return ($user->pointChange( $point, $type, $trade_id));
  }
}
