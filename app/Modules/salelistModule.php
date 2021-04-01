<?php

namespace App\Modules;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Trading;
use App\Models\ProductItem;

use App\Models\SiteConfig;

class salelistModule {
  function needPoint ( $user_id = null){
    $sub = $this->needPointQry($user_id);
    
    if($user_id ) return ( $sub->first() );
    else return ( $sub->get() );
  }
  
  /* 유저 상품 별 */
  function userSaleAvaiInfo( $user_id = null ){
    $siteconfig = SiteConfig::first();
    $sub = $this->needPointQry($user_id);
/* TODO 
페널티 날짜 적용
어드민에서 페널티 적용 해제시 추가 삭제
  diff값에 페널티날짜 마이너스 필요
*/
    $infolist = ProductItem::select( 'user_id','product_id', 'product_name',DB::raw("count(1) as cnt"), DB::raw("sum( sell_price) as sumSalePrice") )
                ->join('products','product_items.product_id','=' ,'products.id')
                ->whereIn('user_id' , function( $query ) use($sub, $siteconfig) {
                    $query->select('users.id' )->from('users')
                          ->joinSub( $sub , 'tmp', function($join) {
                              $join->on('users.id', '=', 'tmp.user_id');
                            })
                    ->whereRaw( " users.point >= tmp.need_point" )
                    ->where( 'penalty_sale','<',$siteconfig->penalty_sale )->where( 'penalty_purchase','<' , $siteconfig->penalty_purchase )
                    ->where(['users.islock'=>'N','login_available'=>'Y' ])
                    ;      
                })
                ->where( ['product_items.is_use'=>'Y' , 'products.is_use'=>'Y'])
                ->whereRaw( "DATEDIFF(NOW(), product_items.buy_date) - product_items.holding_period > products.period")
                ->groupBy('user_id')->groupBy('product_id')
      ;

    return ( $infolist->get() );  
  }
  /* 가능 상품 리스트 */
  function salelist( $user_id = null, $product_id = null,$returnlist = true ){
    $siteconfig = SiteConfig::first();
    $sub = $this->needPointQry($user_id);
    if( $user_id =='ALL') $user_id = null;
    $listsub = DB::table('product_items')
      ->select (
              DB::raw("DATE_ADD( product_items.buy_date ,INTERVAL (products.period + product_items.holding_period ) DAY ) AS saledate"),
              'product_items.id','product_items.product_id','product_items.user_id','product_items.buy_price',
              'product_items.sell_price','product_items.buy_date','product_items.holding_period',
              'product_items.trading_id','product_items.trading_reserved_id',
              'product_items.traded_at','users.phone','users.point','products.product_name',
              'products.profit_rate','products.fee'
             )
      ->join ("users", "product_items.user_id","=", "users.id")
      ->join ("products", "product_items.product_id","=", "products.id")
      ->where( "users.id", ">" ,"1")
      ->where( ['products.is_use'=>'Y', 'product_items.is_use'=>'Y', 'users.islock'=>'N', 'users.login_available'=>'Y'])
      ->where("product_items.trading_reserved_id", "<" ,"1")
      ->whereRaw("DATEDIFF( NOW() , product_items.buy_date) - product_items.holding_period > products.period")
      ->whereIn('user_id' , function( $query ) use($sub, $siteconfig) {
              $query->select('users.id' )->from('users')
                    ->joinSub( $sub , 'tmp', function($join) {
                        $join->on('users.id', '=', 'tmp.user_id');
                      })
              ->whereRaw( " users.point >= tmp.need_point" )
              ->where( 'penalty_sale','<',$siteconfig->penalty_sale )->where( 'penalty_purchase','<' , $siteconfig->penalty_purchase )
              ->where(['users.islock'=>'N','login_available'=>'Y' ])
              ;      
          });
    if( $user_id ) $listsub->where( 'user_id', $user_id);
    if( $product_id ) $listsub->where( 'product_id', $product_id);
    $list = DB::table ( DB::raw("({$listsub->toSql()}) as fromtmp") )->select(DB::raw("concat(user_id,'_',product_id) as id"),'user_id', 'phone', 'point', 'saledate', 'product_id', 'product_name' , DB::raw("COUNT(1) AS amount")
	, DB::raw( "CAST( SUM( sell_price* fee ) /100 /100 AS DECIMAL(10,1) ) AS fee") )->groupByRaw('user_id, saledate, product_id')->orderByRaw('saledate asc, point DESC, user_id asc') ;
    $list->mergeBindings( $listsub );
    
    if (!$returnlist) return $list;
    return ( $list->get() );
        
  }
  private function needPointQry(  $user_id = null ){
    $siteconfig = SiteConfig::first();
    $sub = ProductItem:: 
              select( 'product_items.user_id', 
                      DB::raw( "CAST(sum(product_items.sell_price * products.fee)/100 /".$siteconfig->price_per_point." AS DECIMAL(10,1) ) AS need_point") )
              ->join ( 'products', 'product_items.product_id', '=', 'products.id')
              ->where('product_items.user_id','>', 1 )
              ->whereRaw( "DATEDIFF(NOW(), product_items.buy_date) - product_items.holding_period > products.period")
              ->where( 'product_items.is_use','Y');
    if($user_id ) $sub->where('product_items.user_id', $user_id);
    $sub->groupBy('product_items.user_id');
    return $sub;
  }
  
}
    /*
SELECT users.id, users.point, tmp.need_point
from users
JOIN (
	SELECT 
		product_items.user_id, 
		truncate(sum(product_items.sell_price * products.fee)/100 /100, 1) AS need_point
	FROM product_items
	JOIN products ON product_items.product_id = products.id AND products.is_use = 'Y'
	WHERE product_items.user_id > 0
		#AND product_items.trading_reserved_id < 1 
		AND product_items.is_use='Y'
		AND DATEDIFF(NOW(), product_items.buy_date) > products.period
	GROUP BY user_id
	) tmp ON users.id = tmp.user_id
*/
/*
SELECT
	user_id, phone, point, saledate, product_id, product_name , COUNT(1) AS item_count
	, CAST( SUM( sell_price* fee ) /100 /100 AS DECIMAL(10,1) ) AS fee
from
(
	SELECT 
		DATE_ADD( product_items.buy_date , INTERVAL (products.period + product_items.holding_period ) DAY ) AS saledate
		,	product_items.id, product_items.product_id, product_items.user_id
		, product_items.buy_price , product_items.sell_price , product_items.buy_date
		, product_items.holding_period, product_items.trading_id, product_items.trading_reserved_id
		, product_items.traded_at
		, users.phone, users.point , products.product_name, products.profit_rate, products.fee
	from product_items
	JOIN users ON product_items.user_id = users.id AND users.id > 1
	JOIN products ON product_items.product_id = products.id AND products.is_use = 'Y'
	WHERE 
		product_items.is_use = 'Y'
		AND users.islock = 'N' AND users.login_available='Y'
		AND DATEDIFF( NOW() , product_items.buy_date) - product_items.holding_period > products.period
		AND product_items.trading_reserved_id < 1
		#AND users.id IN(6)
		#AND users.id = 6
) tmp
GROUP BY user_id, saledate, product_id
ORDER BY saledate asc, point DESC, user_id asc
*/