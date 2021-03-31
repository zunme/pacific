<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;
    protected $table = 'product_items';
    protected $fillable = ['product_id', 'parent_item_id', 'user_id','buy_price','sell_price','buy_date','holding_period', 'trading_id','trading_reserved_id', 'is_use','traded_at'];  
    protected $casts = [
        'traded_at' => 'datetime',
    ];
}
