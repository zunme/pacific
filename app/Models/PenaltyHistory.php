<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenaltyHistory extends Model
{
    use HasFactory;
    protected $table = 'penalty_histories';
    protected $fillable = ['user_id', 'penalty_type', 'trade_code','admin'];   
}
