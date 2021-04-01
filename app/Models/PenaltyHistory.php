<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\Trading;
use App\Models\SiteConfig;

use App\Modules\traderModule;

use App\Exceptions\CustomException;

class PenaltyHistory extends Model
{
    use HasFactory;
    protected $table = 'penalty_histories';
    protected $fillable = ['user_id', 'penalty_type', 'trade_code','admin'];  
  
  
    function penalty( $user_id, $penaltytype ,$trade_id=null){
      if( $trade_id ){
        $trade = Trading::find( $trade_id);
        if( !$trade ) throw new CustomException("거래정보를 찾을 수 없습니다.");
      }
      $user = User::find($user_id);
      if( !$user) throw new CustomException("사용자를 찾을 수 없습니다.");

      $siteconfig = SiteConfig::first();
      $data = ['user_id'=>$user->id, 'penalty_type'=> $penaltytype,'trade_id'=>$trade_id  ];

      \DB::beginTransaction();
      try {
        if( $penaltytype == 'sale'){
          $user->increment('penalty_sale');
          $user->increment('penalty_total');
          
          if( $user->penalty_sale >= $siteconfig->penalty_sale ) {
            if( !$user->penalty_start ) $user->penalty_start = Carbon::now();
          }
          if( $trade_id ) {
            //계약완료
              $trader = new traderModule($trade->id, $trade->seller_user_id);
              $trader->trade();
          }
          $user->save();
        }else if( $penaltytype == 'purchase'){
          $user->increment('penalty_purchase');
          $user->increment('penalty_total');
          if ( $user->penalty_purchase >= $siteconfig->penalty_purchase ){
            if( !$user->penalty_start ) $user->penalty_start = Carbon::now();
          }
          if( $trade_id ) {
            //계약취소
            $trader = new traderModule($trade->id);
            $trader->cancel();
          }
          $user->save();
          
        }else if( $penaltytype == 'reset'){
          if( !$user->penalty_start && $user->penalty_sale == 0 && $user->penalty_purchase == 0 ) {
            throw new CustomException("해제할 패널티가 없습니다.");
          }
          if($user->penalty_start) $this->holdingDateAdd($user);
          $user->penalty_sale = 0;
          $user->penalty_purchase = 0;
          $user->penalty_start = null;
          $user->save();
        }else if ( $penaltytype == 'lock'){
          $user->update(['islock'=>'Y' ]);
        }else {
          throw new CustomException("Penalty Type ERROR");
        }
        $log = PenaltyHistory::create( $data );
        \DB::commit();
        return true;
      } catch ( \Exception $e ){
         \DB::rollback();
        throw $e;
      }      
    }
    private function holdingDateAdd( User $user){
       $holdingday = User::select( \DB::raw("IFNULL( DATEDIFF( NOW() , penalty_start ), 0 ) as holding_day") )
         ->where( 'id', $user->id)->first();
       if($holdingday->holding_day > 0 ){
         try{
           \DB::enableQueryLog();
           ProductItem::
             join('products', 'product_items.product_id', '=' ,'products.id')
             ->where("user_id", $user->id)
             ->whereRaw("DATEDIFF(NOW() , buy_date) - holding_period < products.period")
             ->update (["product_items.holding_period"=> \DB::raw("product_items.holding_period + ".$holdingday->holding_day)] );
         } catch( \Exception $e){
           throw $e;
         }
       }
        return true;
    }
}
/*
SELECT 
IFNULL( DATEDIFF( NOW() , penalty_start ) )  INTO @holding_day
FROM users 
WHERE id = 6;

SELECT @holding_day;

UPDATE product_items
JOIN products ON product_items.product_id = products.id
SET product_items.holding_period = product_items.holding_period + @holding_day
WHERE user_id = 6 AND DATEDIFF(NOW() , buy_date) < products.period
;
*/