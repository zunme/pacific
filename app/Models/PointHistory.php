<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;
  
    protected $table = 'point_histories';
    protected $fillable = ['user_id', 'point_type', 'trade_id','admin','amount','before_amount','after_amount'];   
}
