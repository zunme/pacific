<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trading extends Model
{
   use HasFactory;
    protected $table = 'tradings';
    protected $fillable = ['trading_code', 'reservation_id', 'buy_user_id','seller_user_id','product_id','amount','price', 'trading_status','deposit_file','deposit_at','completed_at'];
    protected $casts = [
      'created_at'=>'datetime',
      'deposit_at' => 'datetime',
      'completed_at'=>'datetime',
    ];  
}
