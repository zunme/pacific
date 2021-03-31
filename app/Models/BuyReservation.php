<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyReservation extends Model
{
    use HasFactory;
    protected $table = 'buy_reservations';
    protected $fillable = ['user_id', 'reservation_status', 'reservation_code','product_id','amount' ];   
}
