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
  function userSaleAvaiInfo( $user_id = null ){
    $siteconfig = SiteConfig::first();
    $sub = $this->needPointQry($user_id);
/* TODO 
페널티 날짜 적용
users 에 페널티 시작날짜 컬럼 필요
      페널티 날짜 필요
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
                ->whereRaw( "DATEDIFF(NOW(), product_items.buy_date) > products.period")
                ->groupBy('user_id')->groupBy('product_id')
      ;

    return ( $infolist->get() );  
  }
  private function needPointQry(  $user_id = null ){
    $siteconfig = SiteConfig::first();
    $sub = ProductItem:: 
              select( 'product_items.user_id', 
                      DB::raw( "CAST(sum(product_items.sell_price * products.fee)/100 /".$siteconfig->price_per_point." AS DECIMAL(10,1) ) AS need_point") )
              ->join ( 'products', 'product_items.product_id', '=', 'products.id')
              ->where('product_items.user_id','>', 1 )
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