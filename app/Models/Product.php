<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['product_name', 'is_use', 'period','profit_rate','fee', 'price','image_url'];  
    public function getImageUrl(){
           return Storage::disk('public')
               ->url($this->image_url);
       }  
}
