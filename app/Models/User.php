<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Siteconfig;
use App\Models\PointHistory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'bank_name',
        'bank_account',
        'recommender',
        'penalty_total',
        'penalty_sale',
        'penalty_purchase',
        'login_available',
        'islock',
        'point'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
      'phone_verified_at' => 'datetime',
      'point'=>'decimal:1'
    ];
  public function pointChange( $point, $type, $trade_id=null){
    $siteconfig = Siteconfig::first();
    
    //블럭된 상태에서는 추천포인트 블럭
    if( in_array( $type, array('R1','R2') )){
      if( $this->lslock =='Y' || $this->login_available =='N' 
             ||  $this->penalty_sale >= $siteconfig->penalty_sale  || $this->penalty_purchase >= $siteconfig->penalty_purchase ) return false;  
    }
    
    
    $beforePoint = $this->point;
    // 'user_id'=>$this->id ,
    $pointHistory = [ 'user_id'=>$this->id ,'point_type'=> $type,
                 'amount'=>$point ,'before_amount'=> $this->point, 'after_amount'=>$this->point +  $point,
                 'trade_id'=>$trade_id];
    
    \DB::beginTransaction();
    try {
      $this->point = $this->point + $point; $this->save();
      PointHistory::create($pointHistory);
      \DB::commit();
      return true;
    } catch ( \Exception $e ){
       \DB::rollback();
      throw $e;
      return false;
    }
  }
}
