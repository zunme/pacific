<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    use HasFactory;
    protected $table = 'site_configs';
    protected $fillable = ['site_open', 'point_name', 'price_per_point','max_trading','trading_limit','start_trade', 'end_trade','penalty_sale', 'penalty_purchase'];
}
